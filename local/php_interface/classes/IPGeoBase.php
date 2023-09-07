<?
Class IPGeoBase
{
    protected $baseDirNameRelative = ''; //���������� � ����� ������
    protected $baseDirName = ''; //���������� � ����� ������
    protected $baseCityFileName = ''; //���� � ��������
    protected $baseCIDRFileName = ''; //

    protected $fhandleCIDR = null;
    protected $fhandleCity = null;
    protected $fSizeCIDR = 0;
    protected $fsizeCity = 0;
    protected $oManager = null;


    public function __construct()
    {
		$this->baseDirNameRelative = '/upload/db/';
		$this->baseDirName = $_SERVER['DOCUMENT_ROOT'].$this->baseDirNameRelative;
		$this->baseCityFileName = 'cities.txt';
		$this->baseCIDRFileName = 'cidr_optim.txt';
    }


    /*
     * @brief ��������� ���������� � ������ �� �������
     * @param idx ������ ������
     * @return ������ ��� false, ���� �� �������
     */
    private function getCityByIdx($idx)
    {
        rewind($this->fhandleCity);
        while (!feof($this->fhandleCity)) {
            $str = fgets($this->fhandleCity);
            $arRecord = explode("\t", trim($str));
            if ($arRecord[0] == $idx) {
                return array(
                    'city' => $arRecord[1],
                    'region' => $arRecord[2],
                    'area' => $arRecord[3],
                    'lat' => $arRecord[4],
                    'lng' => $arRecord[5]
                );
            }
        }
        return false;
    }

    /*
     * @brief ��������� ���-���������� �� IP
     * @param ip IPv4-�����
     * @return ������ ��� false, ���� �� �������
     */
    final public function getRecord($ip)
    {

        if (!is_resource($this->fhandleCIDR)) {
            if (file_exists($this->baseDirName . $this->baseCIDRFileName)) {
                $this->fhandleCIDR = fopen($this->baseDirName . $this->baseCIDRFileName, 'r');
                $this->fSizeCIDR = filesize($this->baseDirName . $this->baseCIDRFileName);
            }
        }

        if (!is_resource($this->fhandleCity)) {
            if (file_exists($this->baseDirName . $this->baseCityFileName)) {
                $this->fhandleCity = fopen($this->baseDirName . $this->baseCityFileName, 'r');
                $this->fsizeCity = filesize($this->baseDirName . $this->baseCityFileName);
            }
        }


        $ip = sprintf('%u', ip2long($ip));

        if (!is_resource($this->fhandleCIDR)) return false;
        if (!is_resource($this->fhandleCity)) return false;

        rewind($this->fhandleCIDR);
        $rad = floor($this->fSizeCIDR / 2);
        $pos = $rad;
        while (fseek($this->fhandleCIDR, $pos, SEEK_SET) != -1) {
            if ($rad) {
                $str = fgets($this->fhandleCIDR);
            } else {
                rewind($this->fhandleCIDR);
            }

            $str = fgets($this->fhandleCIDR);

            if (!$str) {
                return false;
            }

            $arRecord = explode("\t", trim($str));

            if (count($arRecord) < 5) return false;

            $rad = floor($rad / 2);
            if (!$rad && ($ip < $arRecord[0] || $ip > $arRecord[1])) {
                return false;
            }

            if ($ip < $arRecord[0]) {
                $pos -= $rad;
            } elseif ($ip > $arRecord[1]) {
                $pos += $rad;
            } else {
                $result = array('range' => $arRecord[2], 'cc' => $arRecord[3]);

                if ($arRecord[4] != '-' && $cityResult = $this->getCityByIdx($arRecord[4])) {
                    $result += $cityResult;
                }

                return $result;
            }
        }
        return false;
    }


    public function updateBase()
    {
        global $APPLICATION;

        //�����
        file_put_contents($this->baseDirName . 'log.txt', '');


        $oHTTP = new \Bitrix\Main\Web\HttpClient();


        $this->log('����� ������ ���������� ���� IPGeo');

        $this->log('�������� ��������� �����');
        CheckDirPath($this->baseDirName . 'tmp/');

        $this->log('��������� ����� ��������� ������ ����');
        if ($result = $oHTTP->download('http://ipgeobase.ru/files/db/Main/geo_files.tar.gz', $this->baseDirName . 'tmp/base.tar.gz')) {
            $oTar = \CBXArchive::GetArchive($this->baseDirName . 'tmp/base.tar.gz');


            $this->log('������������� �����');
            if ($resultTar = $oTar->Unpack($this->baseDirName . 'tmp/')) {
                $this->log('����� ����������');
                $this->log('��������� ������� ������ ��� ������');

                // cidr_optim.txt
                if (file_exists($this->baseDirName . 'tmp/cidr_optim.txt')) {

                    $this->log('���� cidr_optim.txt ������.');
                    $this->log('�������� ������������� ������ cidr_optim.txt ', 1, 1);

                    if ($f = fopen($this->baseDirName . 'tmp/cidr_optim.txt', 'rb')) {

                        $this->log('���� ������ ������', 1, 1);
                        $this->log('��������� ��������� ����', 1, 1);

                        if ($fd = fopen($this->baseDirName . 'tmp/cidr_optim_tmp.txt', 'w')) {

                            $this->log('������ ��������� ����', 1, 1);

                            $i = 0;
                            while (!feof($f)) {

                                fputs($fd, $APPLICATION->ConvertCharset(fgets($f), 'WINDOWS-1251', 'UTF-8'));
                                $i++;
                            }

                            $this->log('���������� ' . $i . ' �����', 1, 1);
                            $this->log('��������� ��������� ����', 1, 1);
                            fclose($fd);

                        } else {
                            $this->log('�� ������� ������� ��������� ����', 0, 1);
                        }

                        $this->log('��������� ���� ������', 1, 1);
                        fclose($f);
                    } else {
                        $this->log('�� ������� ������� ����', 0, 1);
                    }


                    $this->log('�������� ��������� ���� ������ �������', 1, 1);


                    if (file_put_contents($this->baseDirName . 'cidr_optim.txt', file_get_contents($this->baseDirName . 'tmp/cidr_optim_tmp.txt'))) {
                        $this->log('���� ������� ����������', 1, 1);
                    } else {
                        $this->log('�� ������� ����������� ����', 0, 1);
                    }


                } else {
                    $this->log('���� cidr_optim.txt �� ������.', 0);
                }


                // cities.txt
                if (file_exists($this->baseDirName . 'tmp/cities.txt')) {

                    $this->log('���� cities.txt ������.');
                    $this->log('�������� ������������� ������ cities.txt ', 1, 1);

                    if ($f = fopen($this->baseDirName . 'tmp/cities.txt', 'rb')) {

                        $this->log('���� ������ ������', 1, 1);
                        $this->log('��������� ��������� ����', 1, 1);

                        if ($fd = fopen($this->baseDirName . 'tmp/cities_tmp.txt', 'w')) {

                            $this->log('������ ��������� ����', 1, 1);
                            $i = 0;

                            while (!feof($f)) {
                                fputs($fd, $APPLICATION->ConvertCharset(fgets($f), 'WINDOWS-1251', 'UTF-8'));
                                $i++;
                            }
                            $this->log('���������� ' . $i . ' �����', 1, 1);
                            $this->log('��������� ��������� ����', 1, 1);

                            fclose($fd);
                        } else {
                            $this->log('�� ������� ������� ��������� ����', 0, 1);
                        }

                        $this->log('��������� ���� ������', 1, 1);
                        fclose($f);
                    } else {
                        $this->log('�� ������� ������� ����', 0, 1);
                    }


                    $this->log('�������� ��������� ���� ������ �������', 1, 1);

                    if (file_put_contents($this->baseDirName . 'cities.txt', file_get_contents($this->baseDirName . 'tmp/cities_tmp.txt'))) {
                        $this->log('���� ������� ����������', 1, 1);
                    } else {
                        $this->log('�� ������� ����������� ����', 0, 1);
                    }


                } else {
                    $this->log('���� cities.txt �� ������.', 0);
                }


            } else {
                $this->log('�� ������� ����������� �����', 0);
            }

            unset($oTar);

        } else {
            $this->log('�� ������� ������� ����', 0);
        }


        $this->log('������� ��������� �����');

        if (DeleteDirFilesEx($this->baseDirNameRelative . 'tmp/')) {
            $this->log('����� ������� ������� � �������');
        } else {
            $this->log('�� ������� �������� ��������� �����', 0);
        }


        $this->log('���������� ������ ���������');
        unset($oLog, $oHTTP, $result);
    }

    private function log($data, $isSuccess = 1, $iOffsetTab = 0)
    {
        switch ($isSuccess) {
            case 0:
                { // ������
                    file_put_contents($this->baseDirName . 'log.txt', date('d-m-Y H:i:s') . str_repeat("\t", $iOffsetTab + 1) . '/// ERROR /// ' . (is_scalar($data) ? $data : var_export($data, true)) . ' ' . "\r\n", FILE_APPEND);
                }
                break;
            default:
                {
                    file_put_contents($this->baseDirName . 'log.txt', date('d-m-Y H:i:s') . str_repeat("\t", $iOffsetTab + 1) . (is_scalar($data) ? $data : var_export($data, true)) . "\r\n", FILE_APPEND);
                }
                break;
        }
    }


}

