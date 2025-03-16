<?php

namespace App\Repositories\MySQL;

use App\DAO\ReservationDAO;
use App\Repositories\ReservationRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ReservationRepository implements ReservationRepositoryInterface
{
    public static function getTimetablesCountByProductId(UuidInterface $productId): array
    {
        $data = DB::select("-- ReservationGetTimetablesCountByProductId
            SELECT DATE(CONVERT_TZ(time, 'UTC', 'Asia/Bangkok')) AS day, COUNT(*) AS total
            FROM timetables
            WHERE
                product_id = ?
                AND time >= ?
                AND time <= ?
            GROUP BY day
            ORDER BY day
        ", [
            $productId->toString(),
            Carbon::now()->startOfDay()->add('hours', 7)->format('Y-m-d H:i:s'),
            Carbon::now()->startOfDay()->add('hours', 7)->add('days', 14)->format('Y-m-d H:i:s'),
        ]);
        $res = [];
        foreach ($data as $datum) {
            $res[] = new ReservationDAO(
                date: Carbon::parse($datum->day),
                count: $datum->total
            );
        }
        return $res;
    }
    public static function getFilledTimetables(UuidInterface $productId, Carbon $time): array
    {
        $data = DB::select("-- ReservationGetFilledTimetables
            SELECT time FROM timetables
            WHERE
                product_id = ?
                AND time >= ?
                AND time <= ?
        ", [
            $productId->toString(),
            Carbon::parse($time)->startOfDay()->add('hours', 7)->format('Y-m-d H:i:s'),
            Carbon::parse($time)->endOfDay()->add('hours', 7)->format('Y-m-d H:i:s'),
        ]);
        $res = [];
        foreach ($data as $datum) { $res[] = Carbon::parse($datum->time); }
        return $res;
    }

    public static function fillTimetables(UuidInterface $productId, array $time): void
    {
        $insertValues = [];
        $times = [];
        foreach ($time as $datum) {
            $id = Uuid::uuid4()->toString();
            $productIdString = $productId->toString();
            $datetime = $datum->add('hours', -7)->format('Y-m-d H:i:s');
            $times[] = $datetime;
            $insertValues[] = "('$id', '$productIdString', '$datetime')";
        }
        Log::debug(json_encode($times));
        $res = DB::select("-- ReservationFillTimetablesSelect
            SELECT COUNT(*) AS total FROM timetables
            WHERE product_id = ?
                AND time IN (?)
        ", [$productId->toString(), implode(', ', $times)]);
        if ($res[0]->total > 0) {throw new \Exception('TIMETABLE_FILLED'); }
        $insertStatement = implode(', ', $insertValues);
        DB::statement("-- ReservationFillTimetablesInsert
            INSERT INTO timetables (id, product_id, time) VALUES $insertStatement
        ");
    }
}
