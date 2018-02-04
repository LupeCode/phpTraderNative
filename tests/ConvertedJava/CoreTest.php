<?php

namespace ConvertedJava;

use LupeCode\phpTraderNative\ConvertedJava\Core;
use LupeCode\phpTraderNative\ConvertedJava\MAType;
use LupeCode\phpTraderNative\ConvertedJava\MInteger;
use PHPUnit\Framework\TestCase;

/**
 * Class CoreTest
 *
 * Special note about these tests.
 * The PECL Trader package has some features built in that the C and Java packages do not.
 *  - All of the floating point numbers are rounded to a precision of 3
 *  - All of the return values have their return array index adjusted to not start at 0.
 *    The adjusted value is function dependant and relies on the unstable period.
 *    In the C and Java versions, the return array index beings at 0, and outBegIdx holds the adjustment value.
 * These tests have to make those adjustments to verify that the tests are returning the same thing.
 *
 * @package ConvertedJava
 */
class CoreTest extends TestCase
{
    /** @var Core */
    protected static $Core;
    /** @var int */
    protected static $startIdx;
    /** @var int */
    protected static $endIdx;
    /** @var MInteger */
    protected static $outBegIdx;
    /** @var MInteger */
    protected static $outNBElement;
    /** @var array */
    protected $outReal;

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

    protected function adjustForPECL(array $outReal, MInteger $outBegIdx, int $precision = 3, int $mode = \PHP_ROUND_HALF_DOWN)
    {
        $newOutReal = [];
        $outReal    = \array_values($outReal);
        foreach ($outReal as $index => $inDouble) {
            $newOutReal[$index + $outBegIdx->value] = round($inDouble, $precision, $mode);
        }

        return $newOutReal;
    }

    public static function setUpBeforeClass()
    {
        self::$Core         = new Core();
        self::$startIdx     = 0;
        self::$endIdx       = 253;
        self::$outBegIdx    = new MInteger();
        self::$outNBElement = new MInteger();
    }

    public function setUp()
    {
        $this->outReal = array();
    }

    public function testAcos()
    {
        $acosArray = [.1, .2, .3, .4, .5, .6, .7, .8, .9,];
        $endIdx    = 8;
        $RetCode   = self::$Core->acos(self::$startIdx, $endIdx, $acosArray, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_acos($acosArray), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testAd()
    {
        $RetCode = self::$Core->ad(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, $this->Volume, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_ad($this->High, $this->Low, $this->Close, $this->Volume), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testAdd()
    {
        $RetCode = self::$Core->add(self::$startIdx, self::$endIdx, $this->High, $this->Low, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_add($this->High, $this->Low), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testAdOsc()
    {
        $optInFastPeriod = 3;
        $optInSlowPeriod = 10;
        $RetCode         = self::$Core->adOsc(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));

        $optInFastPeriod = 5;
        $optInSlowPeriod = 12;
        $this->outReal   = array();
        $RetCode         = self::$Core->adOsc(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_adosc($this->High, $this->Low, $this->Close, $this->Volume, $optInFastPeriod, $optInSlowPeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testAdx()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->adx(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_adx($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));

        $optInTimePeriod = 20;
        $this->outReal   = array();
        $RetCode         = self::$Core->adx(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_adx($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testAdxr()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->adxr(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_adxr($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));

        $optInTimePeriod = 20;
        $this->outReal   = array();
        $RetCode         = self::$Core->adxr(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_adxr($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));

    }

    public function testApo()
    {
        $optInMAType     = MAType::SMA;
        $optInFastPeriod = 5;
        $optInSlowPeriod = 12;
        $RetCode         = self::$Core->apo(self::$startIdx, self::$endIdx, $this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL($this->outReal, self::$outBegIdx));
        $optInFastPeriod = 7;
        $optInSlowPeriod = 20;
        $this->outReal   = array();
        $RetCode         = self::$Core->apo(self::$startIdx, self::$endIdx, $this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL($this->outReal, self::$outBegIdx));
        $optInMAType   = MAType::EMA;
        $this->outReal = array();
        $RetCode       = self::$Core->apo(self::$startIdx, self::$endIdx, $this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL($this->outReal, self::$outBegIdx));
        $optInMAType   = MAType::WMA;
        $this->outReal = array();
        $RetCode       = self::$Core->apo(self::$startIdx, self::$endIdx, $this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL($this->outReal, self::$outBegIdx));
        $optInMAType   = MAType::DEMA;
        $this->outReal = array();
        $RetCode       = self::$Core->apo(self::$startIdx, self::$endIdx, $this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL($this->outReal, self::$outBegIdx));
        $optInMAType   = MAType::TEMA;
        $this->outReal = array();
        $RetCode       = self::$Core->apo(self::$startIdx, self::$endIdx, $this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL($this->outReal, self::$outBegIdx));
        $optInMAType   = MAType::TRIMA;
        $this->outReal = array();
        $RetCode       = self::$Core->apo(self::$startIdx, self::$endIdx, $this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL($this->outReal, self::$outBegIdx));
        $optInMAType   = MAType::KAMA;
        $this->outReal = array();
        $RetCode       = self::$Core->apo(self::$startIdx, self::$endIdx, $this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL($this->outReal, self::$outBegIdx));
        $optInMAType   = MAType::MAMA;
        $this->outReal = array();
        $RetCode       = self::$Core->apo(self::$startIdx, self::$endIdx, $this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL($this->outReal, self::$outBegIdx));
        $optInMAType   = MAType::T3;
        $this->outReal = array();
        $RetCode       = self::$Core->apo(self::$startIdx, self::$endIdx, $this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_apo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testAroon()
    {
        $optInTimePeriod = 10;
        $outAroonDown    = array();
        $outAroonUp      = array();
        $RetCode         = self::$Core->aroon(self::$startIdx, self::$endIdx, $this->High, $this->Low, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $outAroonDown, $outAroonUp);
        list($traderAroonDown, $traderAroonUp) = \trader_aroon($this->High, $this->Low, $optInTimePeriod);
        $this->assertEquals($traderAroonDown, $this->adjustForPECL($outAroonDown, self::$outBegIdx));
        $this->assertEquals($traderAroonUp, $this->adjustForPECL($outAroonUp, self::$outBegIdx));
    }

    public function testAroonOsc()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->aroonOsc(self::$startIdx, self::$endIdx, $this->High, $this->Low, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_aroonosc($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testAsin()
    {
        $acosArray = [.1, .2, .3, .4, .5, .6, .7, .8, .9,];
        $endIdx    = 8;
        $RetCode   = self::$Core->asin(self::$startIdx, $endIdx, $acosArray, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_asin($acosArray), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testAtan()
    {
        $acosArray = [.1, .2, .3, .4, .5, .6, .7, .8, .9,];
        $endIdx    = 8;
        $RetCode   = self::$Core->atan(self::$startIdx, $endIdx, $acosArray, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_atan($acosArray), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testAtr()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->atr(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_atr($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testAvgPrice()
    {
        $RetCode = self::$Core->avgPrice(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_avgprice($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testBbands()
    {
        $optInTimePeriod   = 10;
        $optInNbDevUp      = 2.0;
        $optInNbDevDn      = 2.0;
        $optInMAType       = MAType::SMA;
        $outRealUpperBand  = array();
        $outRealMiddleBand = array();
        $outRealLowerBand  = array();
        $RetCode           = self::$Core->bbands(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, $optInNbDevUp, $optInNbDevDn, $optInMAType, self::$outBegIdx, self::$outNBElement, $outRealUpperBand, $outRealMiddleBand, $outRealLowerBand);
        list($traderUpperBand, $traderMiddleBand, $traderLowerBand) = \trader_bbands($this->High, $optInTimePeriod, $optInNbDevUp, $optInNbDevDn, $optInMAType);
        $this->assertEquals($traderUpperBand, $this->adjustForPECL($outRealUpperBand, self::$outBegIdx));
        $this->assertEquals($traderMiddleBand, $this->adjustForPECL($outRealMiddleBand, self::$outBegIdx));
        $this->assertEquals($traderLowerBand, $this->adjustForPECL($outRealLowerBand, self::$outBegIdx));
    }

    public function testBeta()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->beta(self::$startIdx, self::$endIdx, $this->High, $this->Low, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_beta($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testBop()
    {
        $RetCode = self::$Core->bop(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_bop($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testCci()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->cci(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_cci($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testCdl2Crows()
    {
        $outInteger = array();
        $RetCode    = self::$Core->cdl2Crows(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdl2crows($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdl3BlackCrows()
    {
        $outInteger = array();
        $RetCode    = self::$Core->cdl3BlackCrows(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdl3blackcrows($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdl3Inside()
    {
        $outInteger = array();
        $RetCode    = self::$Core->cdl3Inside(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdl3inside($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdl3LineStrike()
    {
        $outInteger = array();
        $RetCode    = self::$Core->cdl3LineStrike(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdl3linestrike($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdl3Outside()
    {
        $outInteger = array();
        $RetCode    = self::$Core->cdl3Outside(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdl3outside($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdl3StarsInSouth()
    {
        $outInteger = array();
        $RetCode    = self::$Core->cdl3StarsInSouth(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdl3starsinsouth($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdl3WhiteSoldiers()
    {
        $outInteger = array();
        $RetCode    = self::$Core->cdl3WhiteSoldiers(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdl3whitesoldiers($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlAbandonedBaby()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlAbandonedBaby(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, $optInPenetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlabandonedbaby($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlAdvanceBlock()
    {
        $outInteger = array();
        $RetCode    = self::$Core->cdlAdvanceBlock(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdladvanceblock($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlBeltHold()
    {
        $outInteger = array();
        $RetCode    = self::$Core->cdlBeltHold(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlbelthold($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlBreakaway()
    {
        $outInteger = array();
        $RetCode    = self::$Core->cdlBreakaway(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlbreakaway($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlClosingMarubozu()
    {
        $outInteger = array();
        $RetCode    = self::$Core->cdlClosingMarubozu(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlclosingmarubozu($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlConcealBabysWall()
    {
        $outInteger = array();
        $RetCode    = self::$Core->cdlConcealBabysWall(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlconcealbabyswall($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlCounterAttack()
    {
        $outInteger = array();
        $RetCode    = self::$Core->cdlCounterAttack(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlcounterattack($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlDarkCloudCover()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlDarkCloudCover(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, $optInPenetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdldarkcloudcover($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlDoji()
    {
        $outInteger = array();
        $RetCode    = self::$Core->cdlDoji(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdldoji($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlDojiStar()
    {
        $outInteger = array();
        $RetCode    = self::$Core->cdlDojiStar(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdldojistar($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlDragonflyDoji()
    {
        $outInteger = array();
        $RetCode    = self::$Core->cdlDragonflyDoji(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdldragonflydoji($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlEngulfing()
    {
        $outInteger = array();
        $RetCode    = self::$Core->cdlEngulfing(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlengulfing($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlEveningDojiStar()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlEveningDojiStar(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, $optInPenetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdleveningdojistar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlEveningStar()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlEveningStar(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, $optInPenetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdleveningstar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlGapSideSideWhite()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlGapSideSideWhite(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlgapsidesidewhite($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlGravestoneDoji()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlGravestoneDoji(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlgravestonedoji($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlHammer()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlHammer(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlhammer($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlHangingMan()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlHangingMan(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlhangingman($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlHarami()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlHarami(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlharami($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlHaramiCross()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlHaramiCross(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlharamicross($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlHighWave()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlHighWave(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlhighwave($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlHikkake()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlHikkake(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlhikkake($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlHikkakeMod()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlHikkakeMod(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlhikkakemod($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlHomingPigeon()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlHomingPigeon(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlhomingpigeon($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlIdentical3Crows()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlIdentical3Crows(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlidentical3crows($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlInNeck()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlInNeck(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlinneck($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlInvertedHammer()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlInvertedHammer(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlinvertedhammer($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlKicking()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlKicking(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlkicking($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlKickingByLength()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlKickingByLength(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlkickingbylength($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlLadderBottom()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlLadderBottom(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlladderbottom($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlLongLeggedDoji()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlLongLeggedDoji(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdllongleggeddoji($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlLongLine()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlLongLine(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdllongline($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlMarubozu()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlMarubozu(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlmarubozu($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlMatchingLow()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlMatchingLow(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlmatchinglow($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlMatHold()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlMatHold(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, $optInPenetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlmathold($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlMorningDojiStar()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlMorningDojiStar(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, $optInPenetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlmorningdojistar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlMorningStar()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlMorningStar(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, $optInPenetration, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlmorningstar($this->Open, $this->High, $this->Low, $this->Close, $optInPenetration), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlOnNeck()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlOnNeck(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlonneck($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlPiercing()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlPiercing(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlpiercing($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlRickshawMan()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlRickshawMan(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlrickshawman($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlRiseFall3Methods()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlRiseFall3Methods(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlrisefall3methods($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlSeparatingLines()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlSeparatingLines(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlseparatinglines($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlShootingStar()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlShootingStar(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlshootingstar($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlShortLine()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlShortLine(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlshortline($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlSpinningTop()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlSpinningTop(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlspinningtop($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlStalledPattern()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlStalledPattern(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlstalledpattern($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlStickSandwich()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlStickSandwich(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlsticksandwich($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlTakuri()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlTakuri(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdltakuri($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlTasukiGap()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlTasukiGap(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdltasukigap($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlThrusting()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlThrusting(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlthrusting($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlTristar()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlTristar(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdltristar($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlUnique3River()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlUnique3River(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlunique3river($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlUpsideGap2Crows()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlUpsideGap2Crows(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlupsidegap2crows($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCdlXSideGap3Methods()
    {
        $outInteger       = array();
        $optInPenetration = 1.0;
        $RetCode          = self::$Core->cdlXSideGap3Methods(self::$startIdx, self::$endIdx, $this->Open, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_cdlxsidegap3methods($this->Open, $this->High, $this->Low, $this->Close), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testCeil()
    {
        $RetCode = self::$Core->ceil(self::$startIdx, self::$endIdx, $this->High, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_ceil($this->High), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testCmo()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->cmo(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_cmo($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testCorrel()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->correl(self::$startIdx, self::$endIdx, $this->High, $this->Low, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_correl($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testCos()
    {
        $RetCode = self::$Core->cos(self::$startIdx, self::$endIdx, $this->High, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_cos($this->High), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testCosh()
    {
        $RetCode = self::$Core->cosh(self::$startIdx, self::$endIdx, $this->High, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_cosh($this->High), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testDema()
    {
        $optInTimePeriod = 3;
        $RetCode         = self::$Core->dema(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_dema($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testDiv()
    {
        $RetCode = self::$Core->div(self::$startIdx, self::$endIdx, $this->High, $this->Low, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_div($this->High, $this->Low), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testDx()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->dx(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_dx($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testEma()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->ema(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_ema($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testExp()
    {
        $RetCode = self::$Core->exp(self::$startIdx, self::$endIdx, $this->High, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_exp($this->High), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testFloor()
    {
        $RetCode = self::$Core->floor(self::$startIdx, self::$endIdx, $this->High, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_floor($this->High), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testHtDcPeriod()
    {
        $RetCode = self::$Core->htDcPeriod(self::$startIdx, self::$endIdx, $this->High, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_ht_dcperiod($this->High), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testHtDcPhase()
    {
        $RetCode = self::$Core->htDcPhase(self::$startIdx, self::$endIdx, $this->High, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_ht_dcphase($this->High), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testHtPhasor()
    {
        $outInPhase    = array();
        $outQuadrature = array();
        $RetCode       = self::$Core->htPhasor(self::$startIdx, self::$endIdx, $this->High, self::$outBegIdx, self::$outNBElement, $outInPhase, $outQuadrature);
        $_             = array();
        list($traderInPhase, $traderQuadrature) = \trader_ht_phasor($this->High, $_);
        $this->assertEquals($traderQuadrature, $this->adjustForPECL($outQuadrature, self::$outBegIdx));
        $this->assertEquals($traderInPhase, $this->adjustForPECL($outInPhase, self::$outBegIdx));
    }

    public function testHtSine()
    {
        $outSine     = array();
        $outLeadSine = array();
        $RetCode     = self::$Core->htSine(self::$startIdx, self::$endIdx, $this->High, self::$outBegIdx, self::$outNBElement, $outSine, $outLeadSine);
        $_           = array();
        list($traderSine, $traderLeadSine) = \trader_ht_sine($this->High, $_);
        $this->assertEquals($traderSine, $this->adjustForPECL($outSine, self::$outBegIdx));
        $this->assertEquals($traderLeadSine, $this->adjustForPECL($outLeadSine, self::$outBegIdx));
    }

    public function testHtTrendline()
    {
        $RetCode = self::$Core->htTrendline(self::$startIdx, self::$endIdx, $this->High, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_ht_trendline($this->High), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testHtTrendMode()
    {
        $outInteger = array();
        $RetCode    = self::$Core->htTrendMode(self::$startIdx, self::$endIdx, $this->High, self::$outBegIdx, self::$outNBElement, $outInteger);
        $this->assertEquals(\trader_ht_trendmode($this->High), $this->adjustForPECL($outInteger, self::$outBegIdx));
    }

    public function testKama()
    {
        $optInTimePeriod = 3;
        $RetCode         = self::$Core->kama(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_kama($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testLinearReg()
    {
        $optInTimePeriod = 3;
        $RetCode         = self::$Core->linearReg(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_linearreg($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testLinearRegAngle()
    {
        $optInTimePeriod = 3;
        $RetCode         = self::$Core->linearRegAngle(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_linearreg_angle($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testLinearRegIntercept()
    {
        $optInTimePeriod = 3;
        $RetCode         = self::$Core->linearRegIntercept(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_linearreg_intercept($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testLinearRegSlope()
    {
        $optInTimePeriod = 3;
        $RetCode         = self::$Core->linearRegSlope(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_linearreg_slope($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testLn()
    {
        $RetCode = self::$Core->ln(self::$startIdx, self::$endIdx, $this->High, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_ln($this->High), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testLog10()
    {
        $RetCode = self::$Core->log10(self::$startIdx, self::$endIdx, $this->High, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_log10($this->High), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testMovingAverage()
    {
        $optInTimePeriod = 10;
        $optInMAType     = MAType::SMA;
        $RetCode         = self::$Core->movingAverage(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, $optInMAType, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_ma($this->High, $optInTimePeriod, $optInMAType), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testMacd()
    {
        $optInFastPeriod   = 3;
        $optInSlowPeriod   = 10;
        $optInSignalPeriod = 5;
        $outMACD           = array();
        $outMACDSignal     = array();
        $outMACDHist       = array();
        $RetCode           = self::$Core->macd(self::$startIdx, self::$endIdx, $this->High, $optInFastPeriod, $optInSlowPeriod, $optInSignalPeriod, self::$outBegIdx, self::$outNBElement, $outMACD, $outMACDSignal, $outMACDHist);
        list($traderMACD, $traderMACDSignal, $traderMACDHist) = \trader_macd($this->High, $optInFastPeriod, $optInSlowPeriod, $optInSignalPeriod);
        $this->assertEquals($traderMACD, $this->adjustForPECL($outMACD, self::$outBegIdx));
        $this->assertEquals($traderMACDSignal, $this->adjustForPECL($outMACDSignal, self::$outBegIdx));
        $this->assertEquals($traderMACDHist, $this->adjustForPECL($outMACDHist, self::$outBegIdx));
    }

    public function testMacdExt()
    {
        $optInFastPeriod   = 3;
        $optInFastMAType   = MAType::SMA;
        $optInSlowPeriod   = 10;
        $optInSlowMAType   = MAType::SMA;
        $optInSignalPeriod = 5;
        $optInSignalMAType = MAType::SMA;
        $outMACD           = array();
        $outMACDSignal     = array();
        $outMACDHist       = array();
        $RetCode           = self::$Core->macdExt(self::$startIdx, self::$endIdx, $this->High, $optInFastPeriod, $optInFastMAType, $optInSlowPeriod, $optInSlowMAType, $optInSignalPeriod, $optInSignalMAType, self::$outBegIdx, self::$outNBElement, $outMACD, $outMACDSignal, $outMACDHist);
        list($traderMACD, $traderMACDSignal, $traderMACDHist) = \trader_macdext($this->High, $optInFastPeriod, $optInFastMAType, $optInSlowPeriod, $optInSlowMAType, $optInSignalPeriod, $optInSignalMAType);
        $this->assertEquals($traderMACD, $this->adjustForPECL($outMACD, self::$outBegIdx));
        $this->assertEquals($traderMACDSignal, $this->adjustForPECL($outMACDSignal, self::$outBegIdx));
        $this->assertEquals($traderMACDHist, $this->adjustForPECL($outMACDHist, self::$outBegIdx));
    }

    public function testMacdFix()
    {
        $optInSignalPeriod = 5;
        $outMACD           = array();
        $outMACDSignal     = array();
        $outMACDHist       = array();
        $RetCode           = self::$Core->macdFix(self::$startIdx, self::$endIdx, $this->High, $optInSignalPeriod, self::$outBegIdx, self::$outNBElement, $outMACD, $outMACDSignal, $outMACDHist);
        list($traderMACD, $traderMACDSignal, $traderMACDHist) = \trader_macdfix($this->High, $optInSignalPeriod);
        $this->assertEquals($traderMACD, $this->adjustForPECL($outMACD, self::$outBegIdx));
        $this->assertEquals($traderMACDSignal, $this->adjustForPECL($outMACDSignal, self::$outBegIdx));
        $this->assertEquals($traderMACDHist, $this->adjustForPECL($outMACDHist, self::$outBegIdx));
    }

    public function testMama()
    {
        $optInFastLimit = 0.5;
        $optInSlowLimit = 0.05;
        $outMAMA        = array();
        $outFAMA        = array();
        $RetCode        = self::$Core->mama(self::$startIdx, self::$endIdx, $this->High, $optInFastLimit, $optInSlowLimit, self::$outBegIdx, self::$outNBElement, $outMAMA, $outFAMA);
        list($traderMAMA, $traderFAMA) = \trader_mama($this->High, $optInFastLimit, $optInSlowLimit);
        $this->assertEquals($traderMAMA, $this->adjustForPECL($outMAMA, self::$outBegIdx));
        $this->assertEquals($traderFAMA, $this->adjustForPECL($outFAMA, self::$outBegIdx));
    }

    public function testMovingAverageVariablePeriod()
    {
        $inPeriods      = array_pad(array(), count($this->High), 10);
        $optInMinPeriod = 2;
        $optInMaxPeriod = 20;
        $optInMAType    = MAType::SMA;
        $RetCode        = self::$Core->movingAverageVariablePeriod(self::$startIdx, self::$endIdx, $this->High, $inPeriods, $optInMinPeriod, $optInMaxPeriod, $optInMAType, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_mavp($this->High, $inPeriods, $optInMinPeriod, $optInMaxPeriod, $optInMAType), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testMax()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->max(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_max($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testMaxIndex()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->maxIndex(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_maxindex($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testMedPrice()
    {
        $RetCode = self::$Core->medPrice(self::$startIdx, self::$endIdx, $this->High, $this->Low, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_medprice($this->High, $this->Low), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testMfi()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->mfi(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, $this->Volume, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_mfi($this->High, $this->Low, $this->Close, $this->Volume, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testMidPoint()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->midPoint(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_midpoint($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testMidPrice()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->midPrice(self::$startIdx, self::$endIdx, $this->High, $this->Low, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_midprice($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testMin()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->min(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_min($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testMinIndex()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->minIndex(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_minindex($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testMinMax()
    {
        $outMin          = array();
        $outMax          = array();
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->minMax(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $outMin, $outMax);
        list($traderMin, $traderMax) = \trader_minmax($this->High, $optInTimePeriod);
        $this->assertEquals($traderMin, $this->adjustForPECL($outMin, self::$outBegIdx));
        $this->assertEquals($traderMax, $this->adjustForPECL($outMax, self::$outBegIdx));
    }

    public function testMinMaxIndex()
    {
        $outMin          = array();
        $outMax          = array();
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->minMaxIndex(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $outMin, $outMax);
        list($traderMin, $traderMax) = \trader_minmaxindex($this->High, $optInTimePeriod);
        $this->assertEquals($traderMin, $this->adjustForPECL($outMin, self::$outBegIdx));
        $this->assertEquals($traderMax, $this->adjustForPECL($outMax, self::$outBegIdx));
    }

    public function testMinusDI()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->minusDI(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_minus_di($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testMinusDM()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->minusDM(self::$startIdx, self::$endIdx, $this->High, $this->Low, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_minus_dm($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testMom()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->mom(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_mom($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testMult()
    {
        $RetCode = self::$Core->mult(self::$startIdx, self::$endIdx, $this->Low, $this->High, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_mult($this->Low, $this->High), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testNatr()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->natr(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_natr($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testObv()
    {
        $RetCode = self::$Core->obv(self::$startIdx, self::$endIdx, $this->High, $this->Volume, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_obv($this->High, $this->Volume), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testPlusDI()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->plusDI(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_plus_di($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testPlusDM()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->plusDM(self::$startIdx, self::$endIdx, $this->High, $this->Low, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_plus_dm($this->High, $this->Low, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testPpo()
    {
        $optInFastPeriod = 10;
        $optInSlowPeriod = 10;
        $optInMAType     = MAType::SMA;
        $RetCode         = self::$Core->ppo(self::$startIdx, self::$endIdx, $this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_ppo($this->High, $optInFastPeriod, $optInSlowPeriod, $optInMAType), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testRoc()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->roc(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_roc($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testRocP()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->rocP(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_rocp($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testRocR()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->rocR(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_rocr($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testRocR100()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->rocR100(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_rocr100($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testRsi()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->rsi(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_rsi($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testSar()
    {
        $optInAcceleration = 10;
        $optInMaximum      = 20;
        $RetCode           = self::$Core->sar(self::$startIdx, self::$endIdx, $this->High, $this->Low, $optInAcceleration, $optInMaximum, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_sar($this->High, $this->Low, $optInAcceleration, $optInMaximum), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

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
        $RetCode                    = self::$Core->sarExt(self::$startIdx, self::$endIdx, $this->High, $this->Low, $optInStartValue, $optInOffsetOnReverse, $optInAccelerationInitLong, $optInAccelerationLong, $optInAccelerationMaxLong, $optInAccelerationInitShort, $optInAccelerationShort, $optInAccelerationMaxShort, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_sarext($this->High, $this->Low, $optInStartValue, $optInOffsetOnReverse, $optInAccelerationInitLong, $optInAccelerationLong, $optInAccelerationMaxLong, $optInAccelerationInitShort, $optInAccelerationShort, $optInAccelerationMaxShort), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testSin()
    {
        $RetCode = self::$Core->sin(self::$startIdx, self::$endIdx, $this->High, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_sin($this->High), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testSinh()
    {
        $RetCode = self::$Core->sinh(self::$startIdx, self::$endIdx, $this->High, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_sinh($this->High), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testSma()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->sma(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_sma($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testSqrt()
    {
        $RetCode = self::$Core->sqrt(self::$startIdx, self::$endIdx, $this->High, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_sqrt($this->High), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testStdDev()
    {
        $optInTimePeriod = 10;
        $optInNbDev      = 1;
        $RetCode         = self::$Core->stdDev(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, $optInNbDev, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_stddev($this->High, $optInTimePeriod, $optInNbDev), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testStoch()
    {
        $optInFastK_Period = 2;
        $optInSlowK_Period = 10;
        $optInSlowK_MAType = MAType::SMA;
        $optInSlowD_Period = 20;
        $optInSlowD_MAType = MAType::SMA;
        $outSlowK          = array();
        $outSlowD          = array();
        $RetCode           = self::$Core->stoch(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, $optInFastK_Period, $optInSlowK_Period, $optInSlowK_MAType, $optInSlowD_Period, $optInSlowD_MAType, self::$outBegIdx, self::$outNBElement, $outSlowK, $outSlowD);
        list($traderSlowK, $traderSlowD) = \trader_stoch($this->High, $this->Low, $this->Close, $optInFastK_Period, $optInSlowK_Period, $optInSlowK_MAType, $optInSlowD_Period, $optInSlowD_MAType);
        $this->assertEquals($traderSlowK, $this->adjustForPECL($outSlowK, self::$outBegIdx));
        $this->assertEquals($traderSlowD, $this->adjustForPECL($outSlowD, self::$outBegIdx));
    }

    public function testStochF()
    {
        $optInFastK_Period = 2;
        $optInFastD_Period = 10;
        $optInFastD_MAType = MAType::SMA;
        $outFastK          = array();
        $outFastD          = array();
        $RetCode           = self::$Core->stochF(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, $optInFastK_Period, $optInFastD_Period, $optInFastD_MAType, self::$outBegIdx, self::$outNBElement, $outFastK, $outFastD);
        list($traderFastK, $traderFastD) = \trader_stochf($this->High, $this->Low, $this->Close, $optInFastK_Period, $optInFastD_Period, $optInFastD_MAType);
        $this->assertEquals($traderFastK, $this->adjustForPECL($outFastK, self::$outBegIdx));
        $this->assertEquals($traderFastD, $this->adjustForPECL($outFastD, self::$outBegIdx));
    }

    public function testStochRsi()
    {
        $optInTimePeriod   = 10;
        $optInFastK_Period = 2;
        $optInFastD_Period = 10;
        $optInFastD_MAType = MAType::SMA;
        $outFastK          = array();
        $outFastD          = array();
        $RetCode           = self::$Core->stochRsi(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, $optInFastK_Period, $optInFastD_Period, $optInFastD_MAType, self::$outBegIdx, self::$outNBElement, $outFastK, $outFastD);
        list($traderFastK, $traderFastD) = \trader_stochrsi($this->High, $optInTimePeriod, $optInFastK_Period, $optInFastD_Period, $optInFastD_MAType);
        $this->assertEquals($traderFastK, $this->adjustForPECL($outFastK, self::$outBegIdx));
        $this->assertEquals($traderFastD, $this->adjustForPECL($outFastD, self::$outBegIdx));
    }

    public function testSub()
    {
        $RetCode = self::$Core->sub(self::$startIdx, self::$endIdx, $this->High, $this->Low, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_sub($this->High, $this->Low), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testSum()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->sum(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_sum($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testT3()
    {
        $optInTimePeriod = 10;
        $optInVFactor    = 0.7;
        $RetCode         = self::$Core->t3(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, $optInVFactor, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_t3($this->High, $optInTimePeriod, $optInVFactor), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testTan()
    {
        $RetCode = self::$Core->tan(self::$startIdx, self::$endIdx, $this->High, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_tan($this->High), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testTanh()
    {
        $RetCode = self::$Core->tanh(self::$startIdx, self::$endIdx, $this->High, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_tanh($this->High), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testTema()
    {
        $optInTimePeriod = 3;
        $RetCode         = self::$Core->tema(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_tema($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testTrueRange()
    {
        $RetCode = self::$Core->trueRange(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_trange($this->High, $this->Low, $this->Close), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testTrima()
    {
        $optInTimePeriod = 3;
        $RetCode         = self::$Core->trima(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_trima($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testTrix()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->trix(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_trix($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testTsf()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->tsf(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_tsf($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testTypPrice()
    {
        $RetCode = self::$Core->typPrice(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_typprice($this->High, $this->Low, $this->Close), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testUltOsc()
    {
        $optInTimePeriod1 = 10;
        $optInTimePeriod2 = 11;
        $optInTimePeriod3 = 12;
        $RetCode          = self::$Core->ultOsc(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, $optInTimePeriod1, $optInTimePeriod2, $optInTimePeriod3, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_ultosc($this->High, $this->Low, $this->Close, $optInTimePeriod1, $optInTimePeriod2, $optInTimePeriod3), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testVariance()
    {
        $optInTimePeriod = 10;
        $optInNbDev      = 1.0;
        $RetCode         = self::$Core->variance(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, $optInNbDev, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_var($this->High, $optInTimePeriod, $optInNbDev), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testWclPrice()
    {
        $RetCode = self::$Core->wclPrice(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_wclprice($this->High, $this->Low, $this->Close), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testWillR()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->willR(self::$startIdx, self::$endIdx, $this->High, $this->Low, $this->Close, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_willr($this->High, $this->Low, $this->Close, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

    public function testWma()
    {
        $optInTimePeriod = 10;
        $RetCode         = self::$Core->wma(self::$startIdx, self::$endIdx, $this->High, $optInTimePeriod, self::$outBegIdx, self::$outNBElement, $this->outReal);
        $this->assertEquals(\trader_wma($this->High, $optInTimePeriod), $this->adjustForPECL($this->outReal, self::$outBegIdx));
    }

}
