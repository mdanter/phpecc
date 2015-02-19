<?php

namespace Mdanter\Ecc\Tests;

use Mdanter\Ecc\Curves\CurveFactory;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\PrivateKey;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Signature\Signer;

class HmacRandomNumberGeneratorTest extends AbstractTestCase
{
    /**
     * @var \Mdanter\Ecc\MathAdapterInterface
     */
    protected $math;
    /**
     * @var \Mdanter\Ecc\GeneratorPoint
     */
    protected $G;

    /**
     *
     */
    public function setUp()
    {
        $this->math = EccFactory::getAdapter();
        $this->G = EccFactory::getSecgCurves()->generator256k1();
    }

    public function testCurve()
    {
        $math = EccFactory::getAdapter();
        $G = CurveFactory::getGeneratorByName("nist-p224");
        $algo = 'sha256';

        // Initialize private key and message hash (decimal)
        $privateKey  = $G->getPrivateKeyFrom($this->math->hexDec('F220266E1105BFE3083E03EC7A3A654651F45E37167E88600BF257C1'));
        $messageHash = $this->math->hexDec(hash($algo, "sample"));

        // Derive K
        $drbg = RandomGeneratorFactory::getHmacRandomGenerator($G, $privateKey, $messageHash, $algo);
        $k    = $drbg->generate($this->G->getOrder());
        //$this->assertEquals($this->math->hexdec($test->expectedK), $k);

        $signer = new Signer($this->math);
        $sig    = $signer->sign($privateKey, $messageHash, $k);

        // R and S should be correct
        //$sR = $this->math->hexDec(substr(strtolower($test->expectedRS), 0, 64));
        //$sS = $this->math->hexDec(substr(strtolower($test->expectedRS), 64, 64));

        //$this->assertSame($sR, $sig->getR());
        //$this->assertSame($sS, $sig->getS());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage HMACDRGB: Hashing algorithm not found
     */
    public function testRequireValidAlgorithm()
    {
        $privateKey  = new PrivateKey($this->math, $this->G, 1);
        $hash = hash('sha256', 'message');

        RandomGeneratorFactory::getHmacRandomGenerator($this->G, $privateKey, $hash, 'sha256a');
    }

    public function testDeterministicSign()
    {
        $f = file_get_contents(__DIR__.'/../data/rfc6979.json');
        $json = json_decode($f);

        foreach ($json->test as $test) {
            $G = CurveFactory::getGeneratorByName($test->curve);

            // Initialize private key and message hash (decimal)
            $privateKey  = $G->getPrivateKeyFrom($this->math->hexDec($test->privKey));
            $hashHex     = hash($test->algorithm, $test->message);
            $messageHash = $this->math->hexDec($hashHex);

            // Derive K
            $drbg = RandomGeneratorFactory::getHmacRandomGenerator($G, $privateKey, $messageHash, $test->algorithm);
            $k    = $drbg->generate($G->getOrder());
            $this->assertEquals($this->math->hexdec($test->expectedK), $k, 'k');

            $signer = new Signer($this->math);
            $sig    = $signer->sign($privateKey, $messageHash, $k);

            // R and S should be correct
            $sR = $this->math->hexDec($test->expectedR);
            $sS = $this->math->hexDec($test->expectedS);

            $this->assertTrue($signer->verify($privateKey->getPublicKey(), $sig, $messageHash));

            $this->assertSame($sR, $sig->getR(), 'r');
            echo "k: ".$this->math->decHex($k)." ({$test->expectedK})\n";
            echo "R: ".$this->math->decHex($sig->getR())." ({$test->expectedR})\n";
            echo "S: ".$this->math->decHex($sig->getS())." ({$test->expectedS})\n";
            $this->assertSame($sS, $sig->getS(), 's');
        }
    }

    public function testDeterministicSign2()
    {
        $f = file_get_contents(__DIR__.'/../data/rfc6979.2.json');
        $json = json_decode($f);

        foreach ($json->test as $test) {
            echo "Try {$test->curve} / {$test->algorithm} / '{$test->message}'\n";
            $G = CurveFactory::getGeneratorByName($test->curve);

            // Initialize private key and message hash (decimal)
            $privateKey  = $G->getPrivateKeyFrom($this->math->hexDec($test->privKey));
            $hashHex     = hash($test->algorithm, $test->message);
            $messageHash = $this->math->hexDec($hashHex);

            // Derive K
            $drbg = RandomGeneratorFactory::getHmacRandomGenerator($G, $privateKey, $messageHash, $test->algorithm);
            $k    = $drbg->generate($G->getOrder());
            $this->assertEquals($this->math->hexdec($test->expectedK), $k, 'k');

            $signer = new Signer($this->math);
            $sig    = $signer->sign($privateKey, $messageHash, $k);

            // R and S should be correct
            $sR = $this->math->hexDec($test->expectedR);
            $sS = $this->math->hexDec($test->expectedS);

            $this->assertTrue($signer->verify($privateKey->getPublicKey(), $sig, $messageHash));

            $this->assertSame($sR, $sig->getR(), 'r');
            echo "k: ".$this->math->decHex($k)." ({$test->expectedK})\n";
            echo "R: ".$this->math->decHex($sig->getR())." ({$test->expectedR})\n";
            echo "S: ".$this->math->decHex($sig->getS())." ({$test->expectedS})\n";
            $this->assertSame($sS, $sig->getS(), 's');
        }
    }
}
