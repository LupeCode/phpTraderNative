<?php

namespace LupeCode\phpTraderNativeTest;

use LupeCode\phpTraderNative\TALib\Enum\MovingAverageType;
use LupeCode\phpTraderNative\TALib\Enum\ReturnCode;
use LupeCode\phpTraderNative\Trader;
use PHPUnit\Framework\TestCase;

class TraderTest extends TestCase
{

    //<editor-fold defaultstate="collapsed" desc="Testing Data">
    protected $Open   =
        [
            '37.560001', '36.889999', '36.25', '35.580002', '35.869999', '36.400002', '37.639999', '37.369999',
            '37.369999', '37.27', '37.43', '37.389999', '37.43', '36.630001', '36.700001', '36.970001', '36.82',
            '36.84', '36.900002', '36.669998', '37.110001', '36.66', '37.25', '37.73', '36.880001', '36.130001',
            '36.630001', '36.849998', '36.459999', '36.049999', '36.939999', '37.09', '37.439999', '37.029999',
            '36.91', '37.389999', '37.279999', '37.169998', '36.970001', '36.549999', '37.060001', '35.810001',
            '36.25', '36.650002', '36.509998', '36.84', '37.52', '37.32', '37.470001', '37.50', '37.919998',
            '37.099998', '36.459999', '36.240002', '37.130001', '37.470001', '36.939999', '36.650002',
            '36.380001', '36.849998', '35.860001', '36.400002', '36.700001', '36.549999', '36.540001',
            '36.509998', '36.610001', '35.049999', '35.389999', '34.450001', '35.43', '34.68', '35.00',
            '34.540001', '33.880001', '33.029999', '33.32', '33.75', '33.290001', '33.59', '33.09', '33.66',
            '33.68', '32.91', '32.709999', '32.810001', '32.040001', '31.309999', '31.68', '30.74', '30.41',
            '31.200001', '30.65', '30.190001', '29.559999', '29.469999', '27.98', '27.120001', '26.459999',
            '27.110001', '26.639999', '27.610001', '29.059999', '27.91', '28.450001', '29.32', '29.27', '29.10',
            '30.59', '29.90', '29.76', '29.959999', '29.969999', '28.75', '28.98', '29.84', '29.280001',
            '29.690001', '30.889999', '30.58', '30.65', '30.51', '30.969999', '31.67', '31.549999', '32.119999',
            '33.220001', '33.849998', '33.630001', '33.900002', '34.189999', '34.240002', '32.990002',
            '33.119999', '33.099998', '33.810001', '33.150002', '32.529999', '32.939999', '34.009998',
            '34.490002', '35.799999', '34.240002', '34.759998', '34.34', '35.59', '35.00', '33.869999',
            '33.029999', '32.790001', '32.77', '33.09', '33.00', '32.860001', '33.209999', '32.889999',
            '33.009998', '32.23', '32.779999', '33.200001', '34.209999', '33.450001', '34.07', '34.939999',
            '35.02', '34.889999', '35.150002', '35.459999', '35.139999', '34.869999', '34.299999', '34.970001',
            '33.130001', '32.650002', '31.26', '32.060001', '33.490002', '33.259998', '33.639999', '32.419998',
            '32.279999', '32.560001', '32.709999', '32.349998', '31.790001', '31.309999', '30.790001',
            '30.799999', '28.620001', '28.950001', '28.65', '28.58', '29.030001', '29.66', '29.469999',
            '30.559999', '30.780001', '31.200001', '30.51', '31.309999', '31.139999', '29.719999', '30.68',
            '31.09', '31.35', '30.40', '32.200001', '32.00', '31.860001', '32.009998', '31.49', '32.91',
            '33.32', '33.360001', '32.459999', '32.98', '29.02', '33.580002', '34.77', '35.669998', '35.779999',
            '36.240002', '35.82', '35.080002', '34.290001', '36.740002', '36.869999', '36.52', '37.34', '37.32',
            '36.610001', '36.669998', '37.560001', '37.50', '37.919998', '37.639999', '38.310001', '39.580002',
            '39.299999', '38.57', '39.610001', '39.98', '39.189999', '38.700001', '38.509998', '38.650002',
            '38.25', '38.349998', '38.23', '37.200001', '38.240002', '38.759998', '39.259998', '39.459999',
        ];
    protected $High   =
        [
            '38.080002', '37.580002', '37.080002', '36.139999', '36.009998', '36.950001', '37.779999', '37.52',
            '37.549999', '37.68', '37.439999', '37.450001', '37.599998', '37.439999', '36.91', '37.139999',
            '37.73', '37.299999', '36.990002', '37.200001', '37.110001', '37.220001', '37.740002', '38.080002',
            '37.880001', '36.849998', '36.959999', '37.630001', '36.950001', '36.720001', '37.23', '37.50',
            '37.700001', '37.790001', '37.07', '37.509998', '37.630001', '37.580002', '37.349998', '37.310001',
            '37.450001', '36.09', '36.41', '36.740002', '36.98', '37.439999', '37.59', '37.540001', '37.540001',
            '38.09', '38.119999', '38.189999', '36.73', '37.00', '37.150002', '37.50', '37.349998', '36.830002',
            '36.849998', '36.919998', '37.25', '37.00', '36.919998', '37.50', '36.880001', '37.02', '37.279999',
            '36.43', '35.450001', '34.869999', '35.700001', '35.610001', '36.099998', '35.209999', '34.549999',
            '34.080002', '33.459999', '34.09', '33.860001', '33.599998', '33.52', '33.82', '34.380001', '33.93',
            '33.209999', '33.240002', '32.849998', '32.459999', '31.90', '31.360001', '31.120001', '31.379999',
            '31.209999', '30.23', '30.139999', '29.66', '29.440001', '27.32', '26.969999', '27.809999',
            '27.690001', '27.969999', '29.139999', '29.23', '28.610001', '30.23', '29.790001', '29.51',
            '30.629999', '30.530001', '30.190001', '30.389999', '30.52', '29.799999', '29.110001', '30.00',
            '29.77', '30.57', '31.17', '30.969999', '30.75', '31.540001', '31.190001', '32.459999', '32.330002',
            '32.349998', '33.689999', '34.040001', '34.169998', '33.91', '34.740002', '34.580002', '34.209999',
            '33.299999', '33.630001', '34.080002', '33.880001', '33.389999', '33.27', '34.150002', '34.73',
            '35.84', '34.98', '34.919998', '35.200001', '35.720001', '36.389999', '33.889999', '33.830002',
            '33.09', '33.52', '33.189999', '33.869999', '33.299999', '33.50', '33.02', '33.119999', '32.990002',
            '33.040001', '33.849998', '34.23', '34.130001', '34.080002', '35.200001', '35.299999', '35.240002',
            '35.34', '35.48', '35.700001', '35.150002', '35.27', '35.470001', '33.490002', '33.360001', '31.90',
            '32.43', '33.59', '33.630001', '33.860001', '33.490002', '32.490002', '32.830002', '33.02', '32.689999',
            '32.50', '31.99', '31.190001', '31.200001', '30.709999', '29.00', '29.110001', '29.23', '29.219999',
            '29.709999', '29.57', '30.57', '30.92', '31.74', '30.799999', '31.33', '31.77', '31.23', '30.92',
            '31.43', '31.59', '31.799999', '32.830002', '32.290001', '32.740002', '32.029999', '32.099998',
            '32.990002', '33.57', '33.77', '32.619999', '33.029999', '32.279999', '34.02', '34.950001',
            '35.669998', '36.189999', '36.240002', '36.349998', '36.610001', '34.77', '36.77', '37.689999',
            '36.759998', '37.52', '37.709999', '37.209999', '36.98', '37.57', '37.689999', '37.919998',
            '37.919998', '38.310001', '39.580002', '39.779999', '39.630001', '39.849998', '39.98', '39.790001',
            '38.959999', '38.799999', '39.029999', '38.799999', '38.419998', '38.68', '37.490002', '38.380001',
            '39.119999', '39.639999', '39.779999',
        ];
    protected $Low    =
        [
            '37.43', '36.889999', '36.25', '35.50', '35.049999', '36.09', '37.41', '37.169998', '37.200001',
            '37.240002', '36.810001', '36.540001', '37.099998', '36.630001', '36.419998', '36.59', '36.709999',
            '36.84', '36.630001', '36.52', '36.50', '36.580002', '36.560001', '37.50', '36.84', '36.07',
            '35.470001', '36.77', '36.43', '35.880001', '36.669998', '36.889999', '37.25', '36.880001',
            '36.380001', '36.790001', '37.200001', '37.139999', '36.919998', '36.490002', '36.860001',
            '35.740002', '35.91', '36.32', '36.150002', '36.509998', '36.630001', '36.91', '37.18', '37.16',
            '37.349998', '37.00', '36.110001', '35.880001', '36.419998', '36.849998', '36.900002', '36.279999',
            '36.32', '35.619999', '35.720001', '36.310001', '36.220001', '36.540001', '36.310001', '36.50',
            '36.439999', '35.009998', '34.619999', '33.93', '34.709999', '34.68', '35.00', '34.380001',
            '33.869999', '33.00', '33.110001', '33.509998', '32.84', '32.09', '32.779999', '32.84', '33.59',
            '32.759998', '32.419998', '32.77', '32.040001', '31.309999', '31.219999', '30.24', '29.799999',
            '30.51', '30.389999', '29.700001', '29.389999', '29.059999', '27.940001', '26.719999', '26.15',
            '26.84', '26.51', '26.48', '27.73', '27.709999', '26.57', '28.129999', '28.790001', '28.51',
            '28.60', '29.450001', '29.620001', '29.66', '29.309999', '28.190001', '27.440001', '29.309999',
            '28.59', '28.709999', '29.33', '30.209999', '29.74', '30.00', '30.02', '31.60', '31.530001',
            '31.139999', '33.18', '33.349998', '33.599998', '33.349998', '34.099998', '33.990002', '32.970001',
            '32.689999', '32.869999', '33.110001', '32.91', '32.50', '32.209999', '32.82', '33.91', '33.150002',
            '34.029999', '34.470001', '34.18', '34.099998', '34.77', '33.470001', '32.849998', '32.439999',
            '32.75', '32.610001', '32.869999', '32.689999', '32.580002', '32.32', '32.619999', '32.119999',
            '32.169998', '33.130001', '33.029999', '33.259998', '33.080002', '33.459999', '34.16', '34.75',
            '34.650002', '35.07', '35.049999', '34.700001', '34.299999', '33.880001', '33.00', '32.240002',
            '31.209999', '31.01', '32.77', '33.18', '33.16', '32.400002', '31.77', '32.32', '32.549999',
            '31.99', '31.73', '31.209999', '30.66', '30.35', '28.43', '28.440001', '28.49', '27.85',
            '27.200001', '28.91', '28.85', '29.629999', '30.17', '30.879999', '30.41', '30.809999', '30.99',
            '29.41', '30.00', '30.629999', '31.030001', '30.35', '30.860001', '31.440001', '31.799999',
            '31.379999', '31.23', '32.23', '32.93', '32.950001', '31.110001', '31.540001', '29.00', '32.91',
            '33.869999', '35.029999', '35.599998', '35.77', '35.720001', '34.82', '33.849998', '35.73',
            '36.869999', '36.150002', '36.299999', '37.23', '36.599998', '36.369999', '36.619999', '37.299999',
            '37.380001', '37.27', '37.650002', '38.669998', '39.060001', '38.259998', '39.349998', '39.259998',
            '38.93', '38.599998', '38.150002', '38.439999', '38.099998', '37.779999', '37.52', '36.939999',
            '36.580002', '38.459999', '39.189999', '39.150002',
        ];
    protected $Close  =
        [
            '37.990002', '37.560001', '36.860001', '36.040001', '35.220001', '36.240002', '37.779999',
            '37.360001', '37.400002', '37.290001', '36.939999', '37.389999', '37.32', '37.400002', '36.470001',
            '36.830002', '37.349998', '36.970001', '36.73', '37.07', '36.599998', '37.150002', '36.650002',
            '37.939999', '37.82', '36.759998', '35.59', '37.529999', '36.66', '36.50', '37.02', '37.240002',
            '37.27', '37.48', '36.48', '37.029999', '37.369999', '37.439999', '37.18', '37.23', '36.939999',
            '36.00', '36.009998', '36.529999', '36.599998', '36.59', '36.950001', '37.110001', '37.23', '37.48',
            '37.669998', '37.84', '36.330002', '36.52', '36.509998', '37.169998', '37.310001', '36.66', '36.48',
            '36.07', '36.169998', '36.66', '36.41', '37.02', '36.48', '36.810001', '36.560001', '36.32',
            '35.23', '34.860001', '34.799999', '35.41', '35.470001', '35.169998', '34.279999', '34.009998',
            '33.259998', '33.580002', '33.810001', '32.82', '33.509998', '32.93', '33.959999', '33.860001',
            '32.880001', '32.91', '32.799999', '31.790001', '31.370001', '31.360001', '30.950001', '30.67',
            '31.17', '30.040001', '29.42', '29.370001', '29.280001', '27.040001', '26.76', '27.10', '26.82',
            '27.049999', '27.969999', '29.15', '27.68', '29.059999', '29.57', '29.51', '28.75', '29.690001',
            '29.98', '29.780001', '29.75', '29.309999', '28.780001', '29.74', '29.139999', '30.32', '29.440001',
            '30.690001', '30.17', '30.629999', '30.16', '32.16', '32.200001', '31.40', '33.259998', '33.369999',
            '34.040001', '33.599998', '34.110001', '34.450001', '34.189999', '32.970001', '32.950001', '33.23',
            '33.779999', '33.029999', '32.59', '32.91', '34.630001', '34.400002', '34.849998', '34.68', '34.91',
            '34.34', '35.650002', '33.709999', '33.810001', '32.939999', '33.16', '32.959999', '33.360001',
            '33.110001', '32.630001', '32.98', '32.860001', '32.950001', '32.189999', '33.23', '33.380001',
            '33.990002', '33.68', '34.200001', '35.119999', '35.07', '34.720001', '35.27', '35.619999',
            '35.049999', '35.189999', '34.299999', '33.400002', '33.169998', '31.67', '31.120001', '32.830002',
            '33.50', '33.369999', '33.48', '32.09', '32.34', '32.860001', '32.52', '32.369999', '31.870001',
            '30.959999', '30.85', '30.709999', '28.91', '28.91', '28.26', '27.60', '29.129999', '29.34',
            '29.74', '30.40', '31.17', '30.74', '30.93', '31.40', '31.040001', '30.32', '31.43', '31.15',
            '31.52', '30.90', '31.58', '32.540001', '31.77', '31.60', '32.240002', '33.139999', '33.689999',
            '32.52', '31.74', '31.309999', '32.93', '34.099998', '35.189999', '35.689999', '36.099998',
            '36.240002', '35.93', '34.490002', '36.029999', '37.150002', '36.669998', '36.459999', '37.25',
            '37.119999', '36.689999', '36.669998', '37.419998', '37.669998', '37.720001', '37.84', '38.849998',
            '39.209999', '39.240002', '39.73', '39.540001', '39.68', '38.91', '38.380001', '38.630001',
            '38.759998', '37.919998', '37.610001', '37.23', '38.23', '38.610001', '39.380001', '39.330002',
        ];
    protected $Volume =
        [
            '10602800', '15209900', '10808200', '10990700', '14108600', '19657800', '7710900', '6279200',
            '5574600', '8957300', '12593900', '10634000', '10213200', '13023100', '6519800', '11216500',
            '14759100', '9181900', '14757100', '8426100', '12059100', '12179300', '22177600', '12873600',
            '14645900', '16285700', '33984900', '10623800', '7785800', '34330700', '7127500', '8497900',
            '11340300', '16995100', '11073200', '11170900', '10010800', '7178500', '6157400', '9218200',
            '13718900', '9668700', '9088800', '6585500', '12006800', '13175900', '16401800', '10083100',
            '9832100', '9106700', '11658700', '30761900', '20460600', '17738300', '19046900', '16620000',
            '17166400', '10447800', '13750800', '20273400', '38789100', '19445000', '12443000', '20227300',
            '13656200', '18679000', '19849800', '23603400', '12981000', '14118600', '12217700', '11613900',
            '13551300', '20552600', '9335700', '11002500', '10672300', '8252700', '12380800', '19452000',
            '12653200', '21043800', '22924000', '22946600', '11361400', '10462200', '14850900', '21168500',
            '16683500', '19907400', '12613500', '17072300', '21465100', '20735000', '15319400', '12949700',
            '20187900', '13011900', '11188200', '9098800', '14324800', '24498200', '16379600', '28548200',
            '55707000', '36544700', '12905700', '18979500', '15589300', '13534600', '11426200', '23176900',
            '16298100', '16282500', '20458900', '20053000', '17572600', '15031400', '16692400', '13198500',
            '17140100', '26322800', '21209100', '16047800', '14301100', '25194400', '7423300', '5933500',
            '12621900', '11020800', '3470700', '13469200', '15531300', '9106700', '16921800', '13243500',
            '12689400', '16140900', '15949600', '14936200', '16174200', '45101900', '19852600', '12047900',
            '16109600', '17080500', '56708100', '11020400', '17534700', '5316100', '13257200', '13364700',
            '11899700', '12498500', '14222100', '11105600', '10270400', '11087800', '11063300', '14273100',
            '11804100', '11511000', '13645500', '16608600', '15679100', '17501800', '12394300', '15206700',
            '17418900', '10796200', '15174300', '26380900', '15810300', '24308600', '18501200', '30390400',
            '19349200', '10856500', '12209300', '19403800', '11282800', '15873800', '12580300', '11830400',
            '14245400', '12224800', '10963900', '12883300', '29250500', '14467600', '18317900', '41895400',
            '49541300', '12817500', '19109900', '12755400', '15614900', '18745000', '20538300', '17094200',
            '22179700', '25661600', '22186900', '14365400', '14330900', '47058500', '26873900', '13854100',
            '13748900', '15688100', '19903500', '13609000', '9366100', '19105100', '25414500', '15752300',
            '23163400', '18373600', '15604400', '8976700', '11445300', '7933600', '12241100', '28987700',
            '31405700', '13833200', '11351400', '11845500', '12299700', '6844000', '9051900', '9887300',
            '15446100', '9542700', '10290600', '10333100', '12942700', '8590700', '9267400', '17714800',
            '14861600', '14752100', '14864300', '8462400', '9147300', '9506500', '8713200', '10057600',
            '16909400', '20529200', '19432500', '11803400', '7713000', '6398400',
        ];

    //</editor-fold>

    /**
     * @param array $outReal
     * @param int   $precision
     * @param int   $mode
     *
     * @return array
     */
    protected function adjustForPECL(array $outReal, int $precision = 3, int $mode = \PHP_ROUND_HALF_DOWN)
    {
        $newOutReal = [];
        foreach ($outReal as $index => $inDouble) {
            $newOutReal[$index] = round($inDouble, $precision, $mode);
        }

        return $newOutReal;
    }

    /**
     * @throws \Exception
     * @group exceptions
     */
    public function testAddUnevenParametersError()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(ReturnCode::Messages[ReturnCode::UnevenParameters]);
        $this->expectExceptionCode(6);
        Trader::add([1, 2], [1, 2, 3]);
    }

    /**
     * @throws \Exception
     * @group exceptions
     */
    public function testAddEmptyParametersError()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(ReturnCode::Messages[ReturnCode::OutOfRangeEndIndex]);
        $this->expectExceptionCode(3);
        Trader::add([], []);
    }

    /**
     * @throws \Exception
     * @group exceptions
     */
    public function testAdxBadParameterError()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(ReturnCode::Messages[ReturnCode::BadParam]);
        $this->expectExceptionCode(1);
        Trader::adx([1], [1], [1], 0);
    }

    /**
     * @throws \Exception
     * @group exceptions
     */
    public function testAdOscDefaultsDifferent()
    {
        $this->assertNotEquals(\trader_adosc($this->High, $this->Low, $this->Close, $this->Volume), $this->adjustForPECL(Trader::adosc($this->High, $this->Low, $this->Close, $this->Volume)));
    }

    /**
     * @throws \Exception
     */
    public function testAcos()
    {
        $in = [.1, .2, .3, .4, .5, .6, .7, .8, .9,];
        $this->assertEquals(\trader_acos($in), $this->adjustForPECL(Trader::acos($in)));
    }

    /**
     * @throws \Exception
     */
    public function testAd()
    {
        $this->assertEquals(\trader_ad($this->High, $this->Low, $this->Close, $this->Volume), $this->adjustForPECL(Trader::ad($this->High, $this->Low, $this->Close, $this->Volume)));
    }

    /**
     * @throws \Exception
     */
    public function testAdd()
    {
        $this->assertEquals(\trader_add($this->High, $this->Low), $this->adjustForPECL(Trader::add($this->High, $this->Low)));
    }

    /**
     * @throws \Exception
     */
    public function testAdOsc()
    {
        $optInFastPeriod = 3;
        $optInSlowPeriod = 10;
        $this->assertEquals(\trader_adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod), $this->adjustForPECL(Trader::adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod)));

        $optInFastPeriod = 5;
        $optInSlowPeriod = 12;
        $this->assertEquals(\trader_adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod), $this->adjustForPECL(Trader::adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testAdx()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_adx($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::adx($this->High, $this->Low, $this->Close, $optInTimePeriod)));

        $optInTimePeriod = 20;
        $this->assertEquals(\trader_adx($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::adx($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testAdxr()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_adxr($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::adxr($this->High, $this->Low, $this->Close, $optInTimePeriod)));

        $optInTimePeriod = 20;
        $this->assertEquals(\trader_adxr($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::adxr($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testApo()
    {
        $optInMAType     = MovingAverageType::SMA;
        $optInFastPeriod = 5;
        $optInSlowPeriod = 12;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInFastPeriod = 7;
        $optInSlowPeriod = 20;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::EMA;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::WMA;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::DEMA;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::TEMA;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::TRIMA;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::KAMA;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::MAMA;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
        $optInMAType = MovingAverageType::T3;
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
    }

    /**
     * @throws \Exception
     */
    public function testAroon()
    {
        $optInTimePeriod = 10;
        list($traderAroonDown, $traderAroonUp) = \trader_aroon($this->High, $this->Low, $optInTimePeriod);
        $Output = Trader::aroon($this->High, $this->Low, $optInTimePeriod);
        $this->assertEquals($traderAroonDown, $this->adjustForPECL($Output['AroonDown']));
        $this->assertEquals($traderAroonUp, $this->adjustForPECL($Output['AroonUp']));
    }

    /**
     * @throws \Exception
     */
    public function testAroonOsc()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_aroonosc($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL(Trader::aroonosc($this->High, $this->Low, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testAsin()
    {
        $acosArray = [.1, .2, .3, .4, .5, .6, .7, .8, .9,];
        $this->assertEquals(\trader_asin($acosArray), $this->adjustForPECL(Trader::asin($acosArray)));
    }

    /**
     * @throws \Exception
     */
    public function testAtan()
    {
        $acosArray = [.1, .2, .3, .4, .5, .6, .7, .8, .9,];
        $this->assertEquals(\trader_atan($acosArray), $this->adjustForPECL(Trader::atan($acosArray)));
    }

    /**
     * @throws \Exception
     */
    public function testAtr()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_atr($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::atr($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testAvgPrice()
    {
        $this->assertEquals(\trader_avgprice($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::avgprice($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testBbands()
    {
        $optInTimePeriod = 10;
        $optInNbDevUp    = 2.0;
        $optInNbDevDn    = 2.0;
        $optInMAType     = MovingAverageType::SMA;
        list($traderUpperBand, $traderMiddleBand, $traderLowerBand) = \trader_bbands($this->High, $optInTimePeriod, $optInNbDevUp, $optInNbDevDn, $optInMAType);
        $Output = Trader::bbands($this->High, $optInTimePeriod, $optInNbDevUp, $optInNbDevDn, $optInMAType);
        $this->assertEquals($traderUpperBand, $this->adjustForPECL($Output['UpperBand']));
        $this->assertEquals($traderMiddleBand, $this->adjustForPECL($Output['MiddleBand']));
        $this->assertEquals($traderLowerBand, $this->adjustForPECL($Output['LowerBand']));
    }

    /**
     * @throws \Exception
     */
    public function testBeta()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_beta($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL(Trader::beta($this->High, $this->Low, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testBop()
    {
        $this->assertEquals(\trader_bop($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::bop($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCci()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_cci($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::cci($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testCdl2Crows()
    {
        $this->assertEquals(\trader_cdl2crows($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdl2crows($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdl3BlackCrows()
    {
        $this->assertEquals(\trader_cdl3blackcrows($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdl3blackcrows($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdl3Inside()
    {
        $this->assertEquals(\trader_cdl3inside($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdl3inside($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdl3LineStrike()
    {
        $this->assertEquals(\trader_cdl3linestrike($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdl3linestrike($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdl3Outside()
    {
        $this->assertEquals(\trader_cdl3outside($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdl3outside($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdl3StarsInSouth()
    {
        $this->assertEquals(\trader_cdl3starsinsouth($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdl3starsinsouth($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdl3WhiteSoldiers()
    {
        $this->assertEquals(\trader_cdl3whitesoldiers($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdl3whitesoldiers($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlAbandonedBaby()
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdlabandonedbaby($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(Trader::cdlabandonedbaby($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlAdvanceBlock()
    {
        $this->assertEquals(\trader_cdladvanceblock($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdladvanceblock($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlBeltHold()
    {
        $this->assertEquals(\trader_cdlbelthold($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlbelthold($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlBreakaway()
    {
        $this->assertEquals(\trader_cdlbreakaway($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlbreakaway($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlClosingMarubozu()
    {
        $this->assertEquals(\trader_cdlclosingmarubozu($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlclosingmarubozu($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlConcealBabysWall()
    {
        $this->assertEquals(\trader_cdlconcealbabyswall($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlconcealbabyswall($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlCounterAttack()
    {
        $this->assertEquals(\trader_cdlcounterattack($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlcounterattack($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlDarkCloudCover()
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdldarkcloudcover($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(Trader::cdldarkcloudcover($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlDoji()
    {
        $this->assertEquals(\trader_cdldoji($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdldoji($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlDojiStar()
    {
        $this->assertEquals(\trader_cdldojistar($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdldojistar($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlDragonflyDoji()
    {
        $this->assertEquals(\trader_cdldragonflydoji($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdldragonflydoji($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlEngulfing()
    {
        $this->assertEquals(\trader_cdlengulfing($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlengulfing($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlEveningDojiStar()
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdleveningdojistar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(Trader::cdleveningdojistar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlEveningStar()
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdleveningstar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(Trader::cdleveningstar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlGapSideSideWhite()
    {
        $this->assertEquals(\trader_cdlgapsidesidewhite($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlgapsidesidewhite($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlGravestoneDoji()
    {
        $this->assertEquals(\trader_cdlgravestonedoji($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlgravestonedoji($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlHammer()
    {
        $this->assertEquals(\trader_cdlhammer($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlhammer($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlHangingMan()
    {
        $this->assertEquals(\trader_cdlhangingman($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlhangingman($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlHarami()
    {
        $this->assertEquals(\trader_cdlharami($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlharami($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlHaramiCross()
    {
        $this->assertEquals(\trader_cdlharamicross($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlharamicross($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlHighWave()
    {
        $this->assertEquals(\trader_cdlhighwave($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlhighwave($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlHikkake()
    {
        $this->assertEquals(\trader_cdlhikkake($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlhikkake($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlHikkakeMod()
    {
        $this->assertEquals(\trader_cdlhikkakemod($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlhikkakemod($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlHomingPigeon()
    {
        $this->assertEquals(\trader_cdlhomingpigeon($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlhomingpigeon($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlIdentical3Crows()
    {
        $this->assertEquals(\trader_cdlidentical3crows($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlidentical3crows($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlInNeck()
    {
        $this->assertEquals(\trader_cdlinneck($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlinneck($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlInvertedHammer()
    {
        $this->assertEquals(\trader_cdlinvertedhammer($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlinvertedhammer($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlKicking()
    {
        $this->assertEquals(\trader_cdlkicking($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlkicking($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlKickingByLength()
    {
        $this->assertEquals(\trader_cdlkickingbylength($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlkickingbylength($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlLadderBottom()
    {
        $this->assertEquals(\trader_cdlladderbottom($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlladderbottom($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlLongLeggedDoji()
    {
        $this->assertEquals(\trader_cdllongleggeddoji($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdllongleggeddoji($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlLongLine()
    {
        $this->assertEquals(\trader_cdllongline($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdllongline($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlMarubozu()
    {
        $this->assertEquals(\trader_cdlmarubozu($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlmarubozu($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlMatchingLow()
    {
        $this->assertEquals(\trader_cdlmatchinglow($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlmatchinglow($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlMatHold()
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdlmathold($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(Trader::cdlmathold($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlMorningDojiStar()
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdlmorningdojistar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(Trader::cdlmorningdojistar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlMorningStar()
    {
        $optInPenetration = 1.0;
        $this->assertEquals(\trader_cdlmorningstar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL(Trader::cdlmorningstar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlOnNeck()
    {
        $this->assertEquals(\trader_cdlonneck($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlonneck($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlPiercing()
    {
        $this->assertEquals(\trader_cdlpiercing($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlpiercing($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlRickshawMan()
    {
        $this->assertEquals(\trader_cdlrickshawman($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlrickshawman($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlRiseFall3Methods()
    {
        $this->assertEquals(\trader_cdlrisefall3methods($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlrisefall3methods($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlSeparatingLines()
    {
        $this->assertEquals(\trader_cdlseparatinglines($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlseparatinglines($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlShootingStar()
    {
        $this->assertEquals(\trader_cdlshootingstar($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlshootingstar($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlShortLine()
    {
        $this->assertEquals(\trader_cdlshortline($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlshortline($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlSpinningTop()
    {
        $this->assertEquals(\trader_cdlspinningtop($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlspinningtop($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlStalledPattern()
    {
        $this->assertEquals(\trader_cdlstalledpattern($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlstalledpattern($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlStickSandwich()
    {
        $this->assertEquals(\trader_cdlsticksandwich($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlsticksandwich($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlTakuri()
    {
        $this->assertEquals(\trader_cdltakuri($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdltakuri($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlTasukiGap()
    {
        $this->assertEquals(\trader_cdltasukigap($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdltasukigap($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlThrusting()
    {
        $this->assertEquals(\trader_cdlthrusting($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlthrusting($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlTristar()
    {
        $this->assertEquals(\trader_cdltristar($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdltristar($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlUnique3River()
    {
        $this->assertEquals(\trader_cdlunique3river($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlunique3river($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlUpsideGap2Crows()
    {
        $this->assertEquals(\trader_cdlupsidegap2crows($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlupsidegap2crows($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCdlXSideGap3Methods()
    {
        $this->assertEquals(\trader_cdlxsidegap3methods($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::cdlxsidegap3methods($this->Open, $this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testCeil()
    {
        $this->assertEquals(\trader_ceil($this->High), $this->adjustForPECL(Trader::ceil($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testCmo()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_cmo($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::cmo($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testCorrel()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_correl($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL(Trader::correl($this->High, $this->Low, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testCos()
    {
        $this->assertEquals(\trader_cos($this->High), $this->adjustForPECL(Trader::cos($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testCosh()
    {
        $this->assertEquals(\trader_cosh($this->High), $this->adjustForPECL(Trader::cosh($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testDema()
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_dema($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::dema($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testDiv()
    {
        $this->assertEquals(\trader_div($this->High, $this->Low), $this->adjustForPECL(Trader::div($this->High, $this->Low)));
    }

    /**
     * @throws \Exception
     */
    public function testDx()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_dx($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::dx($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testEma()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_ema($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::ema($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testExp()
    {
        $this->assertEquals(\trader_exp($this->High), $this->adjustForPECL(Trader::exp($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testFloor()
    {
        $this->assertEquals(\trader_floor($this->High), $this->adjustForPECL(Trader::floor($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testHtDcPeriod()
    {
        $this->assertEquals(\trader_ht_dcperiod($this->High), $this->adjustForPECL(Trader::ht_dcperiod($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testHtDcPhase()
    {
        $this->assertEquals(\trader_ht_dcphase($this->High), $this->adjustForPECL(Trader::ht_dcphase($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testHtPhasor()
    {
        list($traderInPhase, $traderQuadrature) = \trader_ht_phasor($this->High);
        $Output = Trader::ht_phasor($this->High);
        $this->assertEquals($traderQuadrature, $this->adjustForPECL($Output['Quadrature']));
        $this->assertEquals($traderInPhase, $this->adjustForPECL($Output['InPhase']));
    }

    /**
     * @throws \Exception
     */
    public function testHtSine()
    {
        list($traderSine, $traderLeadSine) = \trader_ht_sine($this->High);
        $Output = Trader::ht_sine($this->High);
        $this->assertEquals($traderLeadSine, $this->adjustForPECL($Output['LeadSine']));
        $this->assertEquals($traderSine, $this->adjustForPECL($Output['Sine']));
    }

    /**
     * @throws \Exception
     */
    public function testHtTrendline()
    {
        $this->assertEquals(\trader_ht_trendline($this->High), $this->adjustForPECL(Trader::ht_trendline($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testHtTrendMode()
    {
        $this->assertEquals(\trader_ht_trendmode($this->High), $this->adjustForPECL(Trader::ht_trendmode($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testKama()
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_kama($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::kama($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testLinearReg()
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_linearreg($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::linearreg($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testLinearRegAngle()
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_linearreg_angle($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::linearreg_angle($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testLinearRegIntercept()
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_linearreg_intercept($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::linearreg_intercept($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testLinearRegSlope()
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_linearreg_slope($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::linearreg_slope($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testLn()
    {
        $this->assertEquals(\trader_ln($this->High), $this->adjustForPECL(Trader::ln($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testLog10()
    {
        $this->assertEquals(\trader_log10($this->High), $this->adjustForPECL(Trader::log10($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testMovingAverage()
    {
        $optInTimePeriod = 10;
        $optInMAType     = MovingAverageType::SMA;
        $this->assertEquals(\trader_ma($this->High, $optInTimePeriod, $optInMAType), $this->adjustForPECL(Trader::ma($this->High, $optInTimePeriod, $optInMAType)));
    }

    /**
     * @throws \Exception
     */
    public function testMacd()
    {
        $optInFastPeriod   = 3;
        $optInSlowPeriod   = 10;
        $optInSignalPeriod = 5;
        list($traderMACD, $traderMACDSignal, $traderMACDHist) = \trader_macd($this->High, $optInFastPeriod, $optInSlowPeriod, $optInSignalPeriod);
        $Output = Trader::macd($this->High, $optInFastPeriod, $optInSlowPeriod, $optInSignalPeriod);
        $this->assertEquals($traderMACD, $this->adjustForPECL($Output['MACD']));
        $this->assertEquals($traderMACDSignal, $this->adjustForPECL($Output['MACDSignal']));
        $this->assertEquals($traderMACDHist, $this->adjustForPECL($Output['MACDHist']));
    }

    /**
     * @throws \Exception
     */
    public function testMacdExt()
    {
        $optInFastPeriod   = 3;
        $optInFastMAType   = MovingAverageType::SMA;
        $optInSlowPeriod   = 10;
        $optInSlowMAType   = MovingAverageType::SMA;
        $optInSignalPeriod = 5;
        $optInSignalMAType = MovingAverageType::SMA;
        list($traderMACD, $traderMACDSignal, $traderMACDHist) = \trader_macdext($this->High, $optInFastPeriod, $optInFastMAType, $optInSlowPeriod, $optInSlowMAType, $optInSignalPeriod, $optInSignalMAType);
        $Output = Trader::macdext($this->High, $optInFastPeriod, $optInFastMAType, $optInSlowPeriod, $optInSlowMAType, $optInSignalPeriod, $optInSignalMAType);
        $this->assertEquals($traderMACD, $this->adjustForPECL($Output['MACD']));
        $this->assertEquals($traderMACDSignal, $this->adjustForPECL($Output['MACDSignal']));
        $this->assertEquals($traderMACDHist, $this->adjustForPECL($Output['MACDHist']));
    }

    /**
     * @throws \Exception
     */
    public function testMacdFix()
    {
        $optInSignalPeriod = 5;
        list($traderMACD, $traderMACDSignal, $traderMACDHist) = \trader_macdfix($this->High, $optInSignalPeriod);
        $Output = Trader::macdfix($this->High, $optInSignalPeriod);
        $this->assertEquals($traderMACD, $this->adjustForPECL($Output['MACD']));
        $this->assertEquals($traderMACDSignal, $this->adjustForPECL($Output['MACDSignal']));
        $this->assertEquals($traderMACDHist, $this->adjustForPECL($Output['MACDHist']));
    }

    /**
     * @throws \Exception
     */
    public function testMama()
    {
        $optInFastLimit = 0.5;
        $optInSlowLimit = 0.05;
        list($traderMAMA, $traderFAMA) = \trader_mama($this->High, $optInFastLimit, $optInSlowLimit);
        $Output = Trader::mama($this->High, $optInFastLimit, $optInSlowLimit);
        $this->assertEquals($traderMAMA, $this->adjustForPECL($Output['MAMA']));
        $this->assertEquals($traderFAMA, $this->adjustForPECL($Output['FAMA']));
    }

    /**
     * @throws \Exception
     */
    public function testMovingAverageVariablePeriod()
    {
        $inPeriods      = array_pad(array(), count($this->High), 10);
        $optInMinPeriod = 2;
        $optInMaxPeriod = 20;
        $optInMAType    = MovingAverageType::SMA;
        $this->assertEquals(\trader_mavp($this->High, $inPeriods, $optInMinPeriod, $optInMaxPeriod, $optInMAType), $this->adjustForPECL(Trader::mavp($this->High, $inPeriods, $optInMinPeriod, $optInMaxPeriod, $optInMAType)));
    }

    /**
     * @throws \Exception
     */
    public function testMax()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_max($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::max($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMaxIndex()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_maxindex($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::maxindex($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMedPrice()
    {
        $this->assertEquals(\trader_medprice($this->High, $this->Low), $this->adjustForPECL(Trader::medprice($this->High, $this->Low)));
    }

    /**
     * @throws \Exception
     */
    public function testMfi()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_mfi($this->High, $this->Low, $this->Close, $this->Volume, $optInTimePeriod), $this->adjustForPECL(Trader::mfi($this->High, $this->Low, $this->Close, $this->Volume, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMidPoint()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_midpoint($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::midpoint($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMidPrice()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_midprice($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL(Trader::midprice($this->High, $this->Low, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMin()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_min($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::min($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMinIndex()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_minindex($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::minindex($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMinMax()
    {
        $outMin          = array();
        $outMax          = array();
        $optInTimePeriod = 10;
        list($traderMin, $traderMax) = \trader_minmax($this->High, $optInTimePeriod);
        $Output = Trader::minmax($this->High, $optInTimePeriod);
        $this->assertEquals($traderMin, $this->adjustForPECL($Output['Min']));
        $this->assertEquals($traderMax, $this->adjustForPECL($Output['Max']));
    }

    /**
     * @throws \Exception
     */
    public function testMinMaxIndex()
    {
        $outMin          = array();
        $outMax          = array();
        $optInTimePeriod = 10;
        list($traderMin, $traderMax) = \trader_minmaxindex($this->High, $optInTimePeriod);
        $Output = Trader::minmaxindex($this->High, $optInTimePeriod);
        $this->assertEquals($traderMin, $this->adjustForPECL($Output['Min']));
        $this->assertEquals($traderMax, $this->adjustForPECL($Output['Max']));
    }

    /**
     * @throws \Exception
     */
    public function testMinusDI()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_minus_di($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::minus_di($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMinusDM()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_minus_dm($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL(Trader::minus_dm($this->High, $this->Low, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMom()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_mom($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::mom($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testMult()
    {
        $this->assertEquals(\trader_mult($this->Low, $this->High), $this->adjustForPECL(Trader::mult($this->Low, $this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testNatr()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_natr($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::natr($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testObv()
    {
        $this->assertEquals(\trader_obv($this->High, $this->Volume), $this->adjustForPECL(Trader::obv($this->High, $this->Volume)));
    }

    /**
     * @throws \Exception
     */
    public function testPlusDI()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_plus_di($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::plus_di($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testPlusDM()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_plus_dm($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL(Trader::plus_dm($this->High, $this->Low, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testPpo()
    {
        $optInFastPeriod = 10;
        $optInSlowPeriod = 10;
        $optInMAType     = MovingAverageType::SMA;
        $this->assertEquals(\trader_ppo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL(Trader::ppo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType)));
    }

    /**
     * @throws \Exception
     */
    public function testRoc()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_roc($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::roc($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testRocP()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_rocp($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::rocp($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testRocR()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_rocr($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::rocr($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testRocR100()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_rocr100($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::rocr100($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testRsi()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_rsi($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::rsi($this->High, $optInTimePeriod)));
        // A test with real numbers.
        $expected = [
            '42.210139', '45.101929', '43.790895', '44.024530', '44.766276', '42.444434', '48.000712', '43.387497', '50.151878', '54.875765', '46.476423', '40.575072', '45.539758', '47.613588', '44.386741', '41.223737', '49.616844', '50.889708', '53.821251', '50.052066', '48.971100', '53.314404', '52.217529', '51.085649', '49.005748', '44.873849', '50.348316', '39.891732', '44.278588', '47.994485', '46.817654', '49.933871', '55.694793', '53.736302', '55.014014', '55.280020', '58.940617', '50.285986', '44.761536', '43.012268', '51.303298', '54.053435', '49.372593', '46.975564', '44.794978', '49.214314', '41.650776', '46.482220', '49.008324', '47.793580', '47.708699', '47.436434', '48.491563', '36.262877', '39.824684', '34.143726', '43.235154', '38.818494', '41.561516', '38.864092', '35.321889', '31.357898', '34.076185', '37.996955', '35.560439', '38.337523', '35.584957', '40.802948', '40.983594', '36.380568', '35.272427', '36.316932', '32.030886', '28.586394', '32.545789', '28.259153', '26.918714', '34.881916', '32.247423', '30.193482', '27.600608', '27.240716', '22.102399', '19.782925', '18.204042', '24.587512', '23.178912', '31.855723', '42.339904', '37.422332', '40.893819', '46.083348', '45.834278', '44.944830', '53.468048', '49.635845', '48.870488', '50.055282', '50.117523', '43.066245', '44.647382', '50.214816', '46.906383', '49.528304', '56.326269', '54.291897', '54.689855', '53.683162', '56.515458', '60.476269', '59.476124', '62.636911', '67.848693', '70.395688', '68.359165', '69.524440', '70.769607', '70.989704', '59.024694', '59.783842', '59.600880', '63.832334', '57.774446', '52.713437', '55.490157', '61.795305', '64.242416', '69.907488', '58.102394', '60.497014', '57.631898', '63.215569', '59.246658', '52.454033', '48.044407', '46.833006', '46.727265', '48.722093', '48.175699', '47.287333', '49.780555', '47.565390', '48.491029', '43.157875', '47.539249', '50.666455', '57.263833', '51.664946', '55.488415', '60.241130', '60.657133', '59.566447', '61.074004', '62.852393', '59.814512', '57.298068', '52.296049', '57.043103', '44.072413', '41.426100', '34.892326', '40.690291', '49.369603', '48.149120', '50.333699', '43.933833', '43.254177', '45.084004', '46.086954', '44.009508', '40.919572', '38.428973', '35.881079', '35.968983', '27.209610', '29.989026', '28.908348', '28.648929', '32.822456', '38.266650', '37.285247', '45.863047', '47.426056', '50.371855', '45.828722', '51.311700', '50.150082', '41.665223', '48.062853', '50.556842', '52.126783', '46.337265', '56.251859', '55.035151', '54.152187', '54.985480', '51.491515', '59.129101', '61.036646', '61.226782', '54.752799', '57.545952', '38.204380', '56.383780', '59.714488', '62.073404', '62.363481', '63.616679', '61.599866', '58.104685', '54.546423', '62.264369', '62.626982', '60.929376', '63.430451', '63.323977', '59.505277', '59.726294', '62.956195', '62.591746', '64.155933', '62.286086', '64.920742', '69.298878', '67.304416', '62.272428', '66.154737', '67.438507', '62.028564', '58.873861', '57.649489', '58.337041', '55.561625', '56.123615', '55.221124', '48.075094', '54.480397', '57.315600', '59.901817', '60.921764',
        ];
        $result   = Trader::rsi($this->Open, 14);
        $count    = count($expected);
        for ($i = 0; $i < $count; $i++) {
            $expected[$i] = round((float)$expected[$i], 6);
            $actual[$i]   = round($result[$i + 14], 6);
        }
        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws \Exception
     */
    public function testSar()
    {
        $optInAcceleration = 10;
        $optInMaximum      = 20;
        $this->assertEquals(\trader_sar($this->High, $this->Low, $optInAcceleration, $optInMaximum), $this->adjustForPECL(Trader::sar($this->High, $this->Low, $optInAcceleration, $optInMaximum)));
    }

    /**
     * @throws \Exception
     */
    public function testSarExt()
    {
        $optInStartValue            = 0.0;
        $optInOffsetOnReverse       = 0.0;
        $optInAccelerationInitLong  = 2.0;
        $optInAccelerationLong      = 2.0;
        $optInAccelerationMaxLong   = 2.0;
        $optInAccelerationInitShort = 2.0;
        $optInAccelerationShort     = 2.0;
        $optInAccelerationMaxShort  = 2.0;
        $optInAccelerationMaxShort  = 2.0;
        $this->assertEquals(\trader_sarext($this->High, $this->Low, $optInStartValue, $optInOffsetOnReverse, $optInAccelerationInitLong, $optInAccelerationLong, $optInAccelerationMaxLong, $optInAccelerationInitShort, $optInAccelerationShort, $optInAccelerationMaxShort), $this->adjustForPECL(Trader::sarext($this->High, $this->Low, $optInStartValue, $optInOffsetOnReverse, $optInAccelerationInitLong, $optInAccelerationLong, $optInAccelerationMaxLong, $optInAccelerationInitShort, $optInAccelerationShort, $optInAccelerationMaxShort)));
    }

    /**
     * @throws \Exception
     */
    public function testSin()
    {
        $this->assertEquals(\trader_sin($this->High), $this->adjustForPECL(Trader::sin($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testSinh()
    {
        $this->assertEquals(\trader_sinh($this->High), $this->adjustForPECL(Trader::sinh($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testSma()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_sma($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::sma($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testSqrt()
    {
        $this->assertEquals(\trader_sqrt($this->High), $this->adjustForPECL(Trader::sqrt($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testStdDev()
    {
        $optInTimePeriod = 10;
        $optInNbDev      = 1;
        $this->assertEquals(\trader_stddev($this->High, $optInTimePeriod, $optInNbDev), $this->adjustForPECL(Trader::stddev($this->High, $optInTimePeriod, $optInNbDev)));
    }

    /**
     * @throws \Exception
     */
    public function testStoch()
    {
        $optInFastK_Period = 2;
        $optInSlowK_Period = 10;
        $optInSlowK_MAType = MovingAverageType::SMA;
        $optInSlowD_Period = 20;
        $optInSlowD_MAType = MovingAverageType::SMA;
        list($traderSlowK, $traderSlowD) = \trader_stoch($this->High, $this->Low, $this->Close, $optInFastK_Period, $optInSlowK_Period, $optInSlowK_MAType, $optInSlowD_Period, $optInSlowD_MAType);
        $Output = Trader::stoch($this->High, $this->Low, $this->Close, $optInFastK_Period, $optInSlowK_Period, $optInSlowK_MAType, $optInSlowD_Period, $optInSlowD_MAType);
        $this->assertEquals($traderSlowK, $this->adjustForPECL($Output['SlowK']));
        $this->assertEquals($traderSlowD, $this->adjustForPECL($Output['SlowD']));
    }

    /**
     * @throws \Exception
     */
    public function testStochF()
    {
        $optInFastK_Period = 2;
        $optInFastD_Period = 10;
        $optInFastD_MAType = MovingAverageType::SMA;
        list($traderFastK, $traderFastD) = \trader_stochf($this->High, $this->Low, $this->Close, $optInFastK_Period, $optInFastD_Period, $optInFastD_MAType);
        $Output = Trader::stochf($this->High, $this->Low, $this->Close, $optInFastK_Period, $optInFastD_Period, $optInFastD_MAType);
        $this->assertEquals($traderFastK, $this->adjustForPECL($Output['FastK']));
        $this->assertEquals($traderFastD, $this->adjustForPECL($Output['FastD']));
        //Test with real data
        $expectedK = [
            15 => '0.652015751', '0.842490476', '0.703297436', '0.378698449', '0.477942296', '0.13740437', '0.557254111', '0.174244927', '0.915661047', '0.843372667', '0.343282245', '0.045976611', '0.789270962', '0.455938139', '0.394635481', '0.593869121', '0.678161043', '0.689654525', '0.770114264', '0.386972649', '0.597700154', '0.727968303', '0.817426556', '0.737068534', '0.758620259', '0.554972775', '0.126828355', '0.13170543', '0.385364578', '0.419510449', '0.414633373', '0.590243703', '0.724867579', '0.788359147', '0.740425311', '0.810923711', '0.85714309', '0.240816621', '0.31836692', '0.314284466', '0.558440743', '0.619048155', '0.337662197', '0.259740052', '0.175097665', '0.214007393', '0.40466965', '0.307393385', '0.544747471', '0.334630739', '0.632979451', '0.500000798', '0.526104798', '0.211805829', '0.260504482', '0.243697199', '0.414565826', '0.431372829', '0.347338375', '0.112947627', '0.224444', '0.057777333', '0.135514518', '0.218468743', '0.140655133', '0.327188479', '0.209476414', '0.466334148', '0.441396978', '0.197007829', '0.20448888', '0.239747154', '0.148148765', '0.047468957', '0.270531577', '0.25109203', '0.189956467', '0.299126725', '0.072649573', '0.006012222', '0.058271031', '0.223706214', '0.049080039', '0.086036647', '0.141791087', '0.106180682', '0.156521565', '0.347992227', '0.573613876', '0.292543077', '0.57509873', '0.838235294', '0.823529412', '0.580357272', '0.790178971', '0.854910905', '0.810268261', '0.787951997', '0.681927634', '0.554217242', '0.78078837', '0.633004836', '0.923645548', '0.536193173', '0.871313907', '0.731903413', '0.778048293', '0.66341439', '0.940239219', '0.948207549', '0.788844737', '0.931199818', '0.898484545', '0.976703038', '0.897849426', '0.895522274', '0.946395399', '0.889999444', '0.645999942', '0.622362818', '0.680084458', '0.733332722', '0.524999563', '0.40277772', '0.276679909', '0.956521395', '0.603306445', '0.727272251', '0.680440859', '0.743801723', '0.586776973', '0.822967225', '0.358851675', '0.382775598', '0.174641148', '0.227272967', '0.179425837', '0.232911899', '0.169620759', '0.048101772', '0.162162202', '0.132678411', '0.194379859', '0.016393443', '0.259953396', '0.597157063', '0.886256926', '0.739336616', '0.675324886', '0.943396226', '0.92767327', '0.807453787', '0.937500019', '0.977653085', '0.818435297', '0.85754142', '0.603399204', '0.148148834', '0.268785049', '0.102450066', '0.023454366', '0.388060045', '0.530916731', '0.503197974', '0.52665234', '0.230277136', '0.283582029', '0.414798337', '0.338564947', '0.304932443', '0.301754631', '0.093749658', '0.142450102', '0.419889241', '0.088397774', '0.088397774', '0.068219623', '0.063592836', '0.331614834', '0.367697486', '0.43642602', '0.58287799', '0.749056556', '0.739039611', '0.821585864', '0.919037181', '0.840262766', '0.682713278', '0.925601734', '0.864332574', '0.939130626', '0.515075118', '0.685929304', '0.915204436', '0.690058076', '0.640350503', '0.79050291', '0.896634375', '0.981651147', '0.713302752', '0.461538462', '0.48427652', '0.782868526', '0.857142377', '0.92803611', '0.930458961', '0.980662436', '0.985034554', '0.91064377', '0.721419353', '0.904761776', '0.937859947', '0.882623577', '0.858457981', '0.94718725', '0.877083308', '0.735751364', '0.730569759', '0.924870227', '0.938574939', '0.950860688', '0.894618681', '0.87259974', '0.859259225', '0.851240373', '0.967568073', '0.880435087', '0.91689753', '0.70360119', '0.556787103', '0.598214703', '0.54981476', '0.239851661', '0.125461624', '0.095395034', '0.485293815', '0.597058881', '0.823529602', '0.808824005',
        ];
        $expectedD = [
            15 => '0.649246928', '0.67155116', '0.732601221', '0.641495454', '0.519979393', '0.331348372', '0.390866926', '0.289634469', '0.549053361', '0.644426213', '0.700771986', '0.410877174', '0.392843273', '0.430395237', '0.546614861', '0.481480914', '0.555555215', '0.653894896', '0.712643277', '0.615580479', '0.584929022', '0.570880369', '0.714365005', '0.760821131', '0.77103845', '0.683553856', '0.480140463', '0.271168853', '0.214632788', '0.312193486', '0.4065028', '0.474795841', '0.576581552', '0.70115681', '0.751217346', '0.779902723', '0.802830704', '0.636294474', '0.472108877', '0.291156003', '0.39703071', '0.497257788', '0.505050365', '0.405483468', '0.257499971', '0.216281703', '0.264591569', '0.308690143', '0.418936835', '0.395590532', '0.50411922', '0.489203663', '0.553028349', '0.412637142', '0.332805036', '0.23866917', '0.306255836', '0.363211951', '0.39775901', '0.297219611', '0.228243334', '0.131722987', '0.139245284', '0.137253531', '0.164879465', '0.228770785', '0.225773342', '0.334333014', '0.372402513', '0.368246318', '0.280964562', '0.213747954', '0.1974616', '0.145121626', '0.1553831', '0.189697522', '0.237193358', '0.246725074', '0.187244255', '0.125929507', '0.045644275', '0.095996489', '0.110352428', '0.119607633', '0.092302591', '0.111336139', '0.134831112', '0.203564825', '0.35937589', '0.404716394', '0.480418561', '0.5686257', '0.745621145', '0.747373993', '0.731355218', '0.741815716', '0.818452712', '0.817710388', '0.760049298', '0.674698958', '0.672311082', '0.656003482', '0.779146251', '0.697614519', '0.777050876', '0.713136831', '0.793755204', '0.724455365', '0.793900634', '0.850620386', '0.892430502', '0.889417368', '0.872843033', '0.935462467', '0.92434567', '0.923358246', '0.9132557', '0.910639039', '0.827464928', '0.719454068', '0.649482406', '0.678593332', '0.646138914', '0.553703335', '0.40148573', '0.545326341', '0.61216925', '0.762366697', '0.670339852', '0.717171611', '0.670339852', '0.717848641', '0.589531958', '0.521531499', '0.305422807', '0.261563238', '0.193779984', '0.213203568', '0.193986165', '0.150211477', '0.126628245', '0.114314128', '0.163073491', '0.114483904', '0.156908899', '0.291167967', '0.581122462', '0.740916868', '0.76697281', '0.786019243', '0.848798128', '0.892841094', '0.890875692', '0.90753563', '0.911196134', '0.884543267', '0.759791974', '0.536363153', '0.340111029', '0.173127983', '0.13156316', '0.171321492', '0.314143714', '0.47405825', '0.520255682', '0.420042483', '0.346837168', '0.309552501', '0.345648438', '0.352765242', '0.315084007', '0.233478911', '0.17931813', '0.218696334', '0.216912372', '0.19889493', '0.081671723', '0.073403411', '0.154475764', '0.254301718', '0.378579446', '0.462333832', '0.589453522', '0.690324719', '0.769894011', '0.826554219', '0.86029527', '0.814004409', '0.816192593', '0.824215862', '0.909688311', '0.772846106', '0.713378349', '0.705402952', '0.763730605', '0.748537671', '0.706970496', '0.775829263', '0.889596144', '0.863862758', '0.718830787', '0.553039245', '0.576227836', '0.708095808', '0.856015671', '0.905212483', '0.946385836', '0.965385317', '0.958780253', '0.872365892', '0.8456083', '0.854680359', '0.9084151', '0.892980502', '0.896089603', '0.894242846', '0.85334064', '0.78113481', '0.797063783', '0.864671641', '0.938101951', '0.928018102', '0.90602637', '0.875492548', '0.861033112', '0.892689223', '0.899747844', '0.921633563', '0.833644602', '0.725761941', '0.619534332', '0.568272189', '0.462627041', '0.305042681', '0.15356944', '0.235383491', '0.392582577', '0.635294099', '0.743137496',
        ];
        $Output    = Trader::stochf($this->High, $this->Low, $this->Close, 14, 3, $optInFastD_MAType);
        foreach ($expectedK as &$k) {
            $k = round((float)$k * 100, 4);
        }
        foreach ($expectedD as &$d) {
            $d = round((float)$d * 100, 4);
        }
        foreach ($Output['FastK'] as &$k) {
            $k = round((float)$k, 4);
        }
        foreach ($Output['FastD'] as &$d) {
            $d = round((float)$d, 4);
        }
        $this->assertEquals($expectedK, $Output['FastK']);
        $this->assertEquals($expectedD, $Output['FastD']);
    }

    /**
     * @throws \Exception
     */
    public function testStochRsi()
    {
        $optInTimePeriod   = 14;
        $optInFastK_Period = 3;
        $optInFastD_Period = 3;
        $optInFastD_MAType = MovingAverageType::SMA;
        for ($optInFastD_MAType = MovingAverageType::SMA; $optInFastD_MAType < MovingAverageType::MAMA; $optInFastD_MAType++) {
            list($traderFastK, $traderFastD) = \trader_stochrsi($this->Close, $optInTimePeriod, $optInFastK_Period, $optInFastD_Period, $optInFastD_MAType);
            $Output = Trader::stochrsi($this->Close, $optInTimePeriod, $optInFastK_Period, $optInFastD_Period, $optInFastD_MAType);
            $this->assertEquals($traderFastK, $this->adjustForPECL($Output['FastK']));
            $this->assertEquals($traderFastD, $this->adjustForPECL($Output['FastD']));
        }
        $expectedK = [
            29 => '0.480917', '0.695296', '0.785200', '0.798026', '0.891792', '0.452034', '0.708867', '0.861670', '0.952319', '0.853071', '0.882393', '0.407726', '0.000000', '0.008825', '0.463480', '0.523509', '0.515471', '0.847087', '0.991448', '1.000000', '1.000000', '1.000000', '1.000000', '0.108854', '0.222072', '0.216946', '0.603107', '0.642376', '0.228447', '0.123087', '0.000000', '0.069023', '0.392579', '0.249540', '0.622430', '0.313617', '0.750104', '0.538920', '0.340564', '0.000000', '0.000000', '0.000000', '0.397416', '0.434583', '0.298908', '0.000000', '0.000000', '0.000000', '0.176459', '0.300383', '0.060773', '0.493913', '0.389306', '1.000000', '0.957764', '0.577813', '0.596454', '0.554263', '0.203775', '0.077795', '0.074767', '0.000000', '0.000000', '0.348542', '0.042476', '0.000000', '0.000000', '0.000000', '0.000000', '0.000000', '0.191255', '0.194804', '0.367652', '0.997927', '1.000000', '0.728093', '1.000000', '1.000000', '0.989601', '0.860609', '1.000000', '1.000000', '0.964930', '0.955481', '0.865731', '0.741097', '0.966753', '0.723172', '1.000000', '0.500008', '1.000000', '0.747116', '0.947746', '0.708572', '1.000000', '1.000000', '0.721643', '1.000000', '1.000000', '1.000000', '0.853966', '0.944138', '1.000000', '0.871601', '0.322003', '0.313558', '0.409465', '0.417419', '0.000000', '0.000000', '0.131672', '0.728542', '0.614362', '0.756610', '0.665143', '0.743827', '0.490861', '1.000000', '0.000000', '0.040796', '0.000000', '0.075056', '0.007756', '0.154641', '0.062128', '0.000000', '0.130378', '0.088154', '0.124746', '0.000000', '0.357903', '0.922174', '1.000000', '0.809910', '1.000000', '1.000000', '0.976883', '0.812586', '0.985454', '1.000000', '0.745403', '0.788417', '0.421697', '0.000000', '0.000000', '0.000000', '0.000000', '0.409048', '0.539294', '0.514718', '0.537394', '0.283618', '0.339388', '0.529425', '0.454367', '0.593838', '0.492812', '0.207696', '0.175233', '0.132231', '0.000000', '0.000000', '0.000000', '0.000000', '0.551434', '0.617465', '0.743290', '1.000000', '1.000000', '0.904131', '0.949975', '1.000000', '0.914652', '0.755448', '1.000000', '0.937092', '1.000000', '0.747732', '1.000000', '1.000000', '0.605043', '0.521316', '0.802793', '1.000000', '1.000000', '0.542553', '0.280949', '0.000000', '0.558297', '0.880759', '1.000000', '1.000000', '1.000000', '1.000000', '0.888666', '0.428329', '0.724647', '0.903601', '0.761714', '0.699136', '0.837368', '0.654066', '0.396014', '0.384273', '0.657498', '0.744693', '0.762810', '0.972600', '1.000000', '1.000000', '1.000000', '1.000000', '0.839187', '0.886515', '0.242733', '0.000000', '0.104823', '0.160150', '0.000000', '0.000000', '0.000000', '0.288984', '0.384690', '0.560697', '0.542329',
        ];
        $expectedD = [
            29 => '0.6229282', '0.5709972', '0.6538047', '0.7595077', '0.8250061', '0.7139507', '0.6842311', '0.6741906', '0.8409521', '0.8890200', '0.8959277', '0.7143967', '0.4300396', '0.1388503', '0.1574352', '0.3319381', '0.5008200', '0.6286889', '0.7846686', '0.9461782', '0.9971492', '1.0000000', '1.0000000', '0.7029513', '0.4436419', '0.1826238', '0.3473748', '0.4874762', '0.4913099', '0.3313032', '0.1171779', '0.0640365', '0.1538672', '0.2370474', '0.4215166', '0.3951960', '0.5620503', '0.5342138', '0.5431959', '0.2931613', '0.1135212', '0.0000000', '0.1324722', '0.2773330', '0.3769691', '0.2444969', '0.0996360', '0.0000000', '0.0588198', '0.1589473', '0.1792050', '0.2850229', '0.3146640', '0.6277396', '0.7823567', '0.8451925', '0.7106771', '0.5761766', '0.4514972', '0.2786109', '0.1187788', '0.0508538', '0.0249222', '0.1161807', '0.1303394', '0.1303394', '0.0141587', '0.0000000', '0.0000000', '0.0000000', '0.0637517', '0.1286864', '0.2512370', '0.5201277', '0.7885263', '0.9086734', '0.9093644', '0.9093644', '0.9965338', '0.9500702', '0.9500702', '0.9535364', '0.9883100', '0.9734704', '0.9287141', '0.8541032', '0.8578604', '0.8103408', '0.8966417', '0.7410601', '0.8333360', '0.7490415', '0.8982874', '0.8011447', '0.8854392', '0.9028572', '0.9072144', '0.9072144', '0.9072144', '1.0000000', '0.9513220', '0.9327014', '0.9327014', '0.9385798', '0.7312015', '0.5023876', '0.3483423', '0.3801475', '0.2756281', '0.1391397', '0.0438907', '0.2867379', '0.4915253', '0.6998378', '0.6787050', '0.7218600', '0.6332770', '0.7448959', '0.4969535', '0.3469319', '0.0135986', '0.0386171', '0.0276039', '0.0791511', '0.0748418', '0.0722564', '0.0641685', '0.0728440', '0.1144260', '0.0709667', '0.1608830', '0.4266924', '0.7600257', '0.9106946', '0.9366365', '0.9366365', '0.9922943', '0.9298231', '0.9249744', '0.9326802', '0.9102857', '0.8446065', '0.6518387', '0.4033711', '0.1405655', '-0.0000000', '-0.0000000', '0.1363494', '0.3161141', '0.4876867', '0.5304684', '0.4452432', '0.3868000', '0.3841439', '0.4410602', '0.5258768', '0.5136725', '0.4314486', '0.2919135', '0.1717197', '0.1024879', '0.0440770', '-0.0000000', '-0.0000000', '0.1838112', '0.3896330', '0.6373964', '0.7869185', '0.9144301', '0.9680437', '0.9513688', '0.9513688', '0.9548759', '0.8900334', '0.8900334', '0.8975135', '0.9790308', '0.8949416', '0.9159108', '0.9159108', '0.8683476', '0.7087864', '0.6430508', '0.7747031', '0.9342643', '0.8475175', '0.6078340', '0.2745007', '0.2797488', '0.4796852', '0.8130185', '0.9602529', '1.0000000', '1.0000000', '0.9628887', '0.7723318', '0.6805476', '0.6855260', '0.7966543', '0.7881506', '0.7660728', '0.7301900', '0.6291492', '0.4781174', '0.4792615', '0.5954879', '0.7216668', '0.8267009', '0.9118032', '0.9908667', '1.0000000', '1.0000000', '0.9463956', '0.9085673', '0.6561450', '0.3764161', '0.1158520', '0.0883243', '0.0883243', '0.0533834', '-0.0000000', '0.0963281', '0.2245580', '0.4114569', '0.4959053',
        ];
        $Output    = Trader::stochrsi($this->Close, 14, 14, 3, MovingAverageType::SMA);
        foreach ($expectedK as &$k) {
            $k = round((float)$k * 100, 4);
        }
        foreach ($expectedD as &$d) {
            $d = round((float)$d * 100, 4);
        }
        foreach ($Output['FastK'] as &$k) {
            $k = round((float)$k, 4);
        }
        foreach ($Output['FastD'] as &$d) {
            $d = round((float)$d, 4);
        }
        $this->assertEquals($expectedK, $Output['FastK']);
        $this->assertEquals($expectedD, $Output['FastD']);
    }

    /**
     * @throws \Exception
     */
    public function testStochRstVsStochAndRsi()
    {
        $rsi      = Trader::rsi($this->Close, 14);
        $stoch    = Trader::stochf($rsi, $rsi, $rsi, 14, 3, MovingAverageType::SMA);
        $stochRsi = Trader::stochrsi($this->Close, 14, 14, 3, MovingAverageType::SMA);
        $this->assertEquals(array_values($stoch['FastK']), array_values($stochRsi['FastK']));
        $this->assertEquals(array_values($stoch['FastD']), array_values($stochRsi['FastD']));
    }

    /**
     * @throws \Exception
     */
    public function testSub()
    {
        $this->assertEquals(\trader_sub($this->High, $this->Low), $this->adjustForPECL(Trader::sub($this->High, $this->Low)));
    }

    /**
     * @throws \Exception
     */
    public function testSum()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_sum($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::sum($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testT3()
    {
        $optInTimePeriod = 10;
        $optInVFactor    = 0.7;
        $this->assertEquals(\trader_t3($this->High, $optInTimePeriod, $optInVFactor), $this->adjustForPECL(Trader::t3($this->High, $optInTimePeriod, $optInVFactor)));
    }

    /**
     * @throws \Exception
     */
    public function testTan()
    {
        $this->assertEquals(\trader_tan($this->High), $this->adjustForPECL(Trader::tan($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testTanh()
    {
        $this->assertEquals(\trader_tanh($this->High), $this->adjustForPECL(Trader::tanh($this->High)));
    }

    /**
     * @throws \Exception
     */
    public function testTema()
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_tema($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::tema($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testTrueRange()
    {
        $this->assertEquals(\trader_trange($this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::trange($this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testTrima()
    {
        $optInTimePeriod = 3;
        $this->assertEquals(\trader_trima($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::trima($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testTrix()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_trix($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::trix($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testTsf()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_tsf($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::tsf($this->High, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testTypPrice()
    {
        $this->assertEquals(\trader_typprice($this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::typprice($this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testUltOsc()
    {
        $optInTimePeriod1 = 10;
        $optInTimePeriod2 = 11;
        $optInTimePeriod3 = 12;
        $this->assertEquals(\trader_ultosc($this->High, $this->Low, $this->Close, $optInTimePeriod1, $optInTimePeriod2, $optInTimePeriod3), $this->adjustForPECL(Trader::ultosc($this->High, $this->Low, $this->Close, $optInTimePeriod1, $optInTimePeriod2, $optInTimePeriod3)));
    }

    /**
     * @throws \Exception
     */
    public function testVariance()
    {
        $optInTimePeriod = 10;
        $optInNbDev      = 1.0;
        $this->assertEquals(\trader_var($this->High, $optInTimePeriod, $optInNbDev), $this->adjustForPECL(Trader::var($this->High, $optInTimePeriod, $optInNbDev)));
    }

    /**
     * @throws \Exception
     */
    public function testWclPrice()
    {
        $this->assertEquals(\trader_wclprice($this->High, $this->Low, $this->Close), $this->adjustForPECL(Trader::wclprice($this->High, $this->Low, $this->Close)));
    }

    /**
     * @throws \Exception
     */
    public function testWillR()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_willr($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL(Trader::willr($this->High, $this->Low, $this->Close, $optInTimePeriod)));
    }

    /**
     * @throws \Exception
     */
    public function testWma()
    {
        $optInTimePeriod = 10;
        $this->assertEquals(\trader_wma($this->High, $optInTimePeriod), $this->adjustForPECL(Trader::wma($this->High, $optInTimePeriod)));
    }
}
