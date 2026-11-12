<?php

namespace App\Controller\Api;

use App\Entity\Performance;
use App\Repository\PerformanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * What the Quotas tool needs from the theatre's own ticketing: read the reservations of each performance
 * and cap the seats offered online. Reachable only with the Quotas token (see QuotasTokenHandler).
 *
 * Reservations are exposed as a count per performance, with no personal data: a deleted reservation
 * simply lowers the count, so the tool never keeps a booking that no longer exists.
 */
#[Route('/api/quotas')]
class QuotasApiController extends AbstractController
{
    private const TIMEZONE = 'Europe/Paris';

    public function __construct(private readonly PerformanceRepository $performances)
    {
    }

    /**
     * Query: ids (comma separated), show (id), from (ISO 8601, default: yesterday).
     */
    #[Route('/performances', name: 'api_quotas_performances', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $ids = [];
        if ('' !== (string) $request->query->get('ids', '')) {
            foreach (explode(',', (string) $request->query->get('ids')) as $id) {
                if (!ctype_digit(trim($id))) {
                    return $this->error('"ids" must be a comma separated list of integers.', 400);
                }
                $ids[] = (int) $id;
            }
        }

        $show = $request->query->get('show');
        if (null !== $show && !ctype_digit((string) $show)) {
            return $this->error('"show" must be an integer.', 400);
        }

        try {
            $from = new \DateTimeImmutable((string) $request->query->get('from', 'yesterday'), new \DateTimeZone(self::TIMEZONE));
        } catch (\Exception) {
            return $this->error('"from" must be an ISO 8601 date.', 400);
        }

        $rows = $this->performances->findWithReservedCount($ids, null === $show ? null : (int) $show, $this->toStoredTime($from));

        return $this->json(['performances' => array_map($this->present(...), $rows)]);
    }

    /** Body: {"quota": 12}. Refused when below the seats already reserved. */
    #[Route('/performances/{id}/quota', name: 'api_quotas_set_quota', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function setQuota(Performance $performance, Request $request, ValidatorInterface $validator, EntityManagerInterface $em): JsonResponse
    {
        try {
            $quota = $request->getPayload()->get('quota');
        } catch (\Exception) {
            return $this->error('The body must be a JSON object.', 400);
        }
        if (!is_int($quota) || $quota < 0) {
            return $this->error('"quota" must be a non-negative integer.', 400);
        }

        $performance->setQuota($quota);
        $violations = $validator->validate($performance);
        if (count($violations) > 0) {
            $em->refresh($performance);

            return $this->error((string) $violations->get(0)->getMessage(), 422);
        }
        $em->flush();

        return $this->json($this->present($this->performances->findWithReservedCount([$performance->getId()])[0]));
    }

    /**
     * performedAt holds the wall-clock time of the theatre (read as UTC by PHP): the offset is made explicit here.
     *
     * @param array{id: int, projectId: int, projectName: string, performedAt: \DateTimeImmutable, quota: int, reserved: int} $row
     *
     * @return array<string, mixed>
     */
    private function present(array $row): array
    {
        $startsAt = new \DateTimeImmutable($row['performedAt']->format('Y-m-d H:i:s'), new \DateTimeZone(self::TIMEZONE));

        return [
            'id' => $row['id'],
            'show' => ['id' => $row['projectId'], 'name' => $row['projectName']],
            'startsAt' => $startsAt->format(\DATE_ATOM),
            'quota' => $row['quota'],
            'reserved' => $row['reserved'],
        ];
    }

    private function toStoredTime(\DateTimeImmutable $moment): \DateTimeImmutable
    {
        return new \DateTimeImmutable($moment->setTimezone(new \DateTimeZone(self::TIMEZONE))->format('Y-m-d H:i:s'), new \DateTimeZone('UTC'));
    }

    private function error(string $message, int $status): JsonResponse
    {
        return $this->json(['error' => $message], $status);
    }
}
