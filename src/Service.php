<?php

namespace Minhyung\KoreaWeather;

use Carbon\Carbon;
use GuzzleHttp\Client;
use RuntimeException;

class Service
{
    private ?Client $client = null;

    const BASE_URI = 'http://apis.data.go.kr/1360000/VilageFcstInfoService_2.0/';

    public function __construct(
        private string $serviceKey
    ) {
        //
    }

    /**
     * 초단기실황조회
     * 
     * @param  int  $nx
     * @param  int  $ny
     * @param  \DateTimeInterface|string|null  $datetime
     * @param  int  $limit
     * @param  int  $page
     * @return array
     * @throws \RuntimeException
     */
    public function getUltraSrtNcst($nx, $ny, $datetime = null, $limit = 100, $page = 1)
    {
        $datetime = Carbon::parse($datetime, 'Asia/Seoul');

        // 매시각 정시에 발표되고, 10분 이후에 호출가능.
        if ($datetime->minute < 10) {
            $datetime->subHour();
        }

        $params = [
            'base_date' => $datetime->format('Ymd'),
            'base_time' => $datetime->format('H00'),
            'nx' => $nx,
            'ny' => $ny,
            'numOfRows' => $limit,
            'pageNo' => $page,
        ];
        return $this->request('getUltraSrtNcst', $params);
    }

    /**
     * 초단기예보조회
     * 
     * @param  int  $nx
     * @param  int  $ny
     * @param  \DateTimeInterface|string|null  $datetime
     * @param  int  $limit
     * @param  int  $page
     * @return array
     * @throws \RuntimeException
     */
    public function getUltraSrtFcst($nx, $ny, $datetime = null, $limit = 100, $page = 1)
    {
        $datetime = Carbon::parse($datetime, 'Asia/Seoul');

        // 매시간 30분에 생성되고 45분 이후로 제공
        if ($datetime->minute < 45) {
            $datetime->subHour();
        }

        $params = [
            'base_date' => $datetime->format('Ymd'),
            'base_time' => $datetime->format('H00'),
            'nx' => $nx,
            'ny' => $ny,
            'numOfRows' => $limit,
            'pageNo' => $page,
        ];
        return $this->request('getUltraSrtFcst', $params);
    }

    /**
     * 단기예보조회
     * 
     * @param  int  $nx
     * @param  int  $ny
     * @param  \DateTimeInterface|string|null  $datetime
     * @param  int  $limit
     * @param  int  $page
     * @return array
     * @throws \RuntimeException
     */
    public function getVilageFcst($nx, $ny, $datetime = null, $limit = 100, $page = 1)
    {
        $datetime = Carbon::parse($datetime, 'Asia/Seoul');
        
        // 2시부터 3시간 간격으로 8회 생성되고, 10분이 지난 후부터 호출가능
        if ($datetime->minute < 10) {
            $datetime->subHours(3);
        }
        
        if ($datetime->hour >= 2) {
            $datetime->hour = ((int) floor($datetime->hour / 3) * 3) + 2;
        } else {
            $datetime->subDay()->setHour(23);
        }

        $params = [
            'base_date' => $datetime->format('Ymd'),
            'base_time' => $datetime->format('H00'),
            'nx' => $nx,
            'ny' => $ny,
            'numOfRows' => $limit,
            'pageNo' => $page,
        ];
        return $this->request('getVilageFcst', $params);
    }

    /**
     * 상세기능정보
     * 
     * @param  string  $ftype  (ODAM|VSRT|SHRT)
     * @param  \DateTimeInterface|string
     * @param  int  $limit
     * @param  int  $page
     */
    public function getFcstVersion($ftype, $datetime, $limit = 100, $page = 1)
    {
        $datetime = Carbon::parse($datetime, 'Asia/Seoul');
        $params = [
            'basedatetime' => $datetime->format('YmdHi'),
            'ftype' => $ftype,
            'numOfRows' => $limit,
            'pageNo' => $page,
        ];
        return $this->request('getFcstVersion', $params);
    }

    protected function client(): Client
    {
        if (is_null($this->client)) {
            $this->client = new Client([
                'base_uri' => self::BASE_URI,
                'http_errors' => false,
            ]);
        }
        return $this->client;
    }

    protected function request($path, $params)
    {
        $params += ['serviceKey' => $this->serviceKey, 'dataType' => 'JSON'];
        $response = $this->client()->get($path, ['query' => $params]);
        $responseData = json_decode((string) $response->getBody(), true)['response'];
        $header = $responseData['header'];

        if ($header['resultCode'] !== '00') {
            throw new RuntimeException($header['resultMsg']);
        }
        return $responseData['body'];
    }
}