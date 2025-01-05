<?php

namespace Tests;

use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Illuminate\Foundation\Testing\DatabaseMigrations;

abstract class DuskTestCase extends BaseTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    public static function prepare(): void
    {
        // On s'assure que ChromeDriver n'est pas déjà en cours d'exécution
        if (static::isChromedriverRunning()) {
            static::stopChromeDriver();
        }

        // On attend un peu avant de démarrer ChromeDriver
        sleep(2);
        static::startChromeDriver();
    }

    protected static function isChromedriverRunning()
    {
        try {
            $fp = fsockopen('localhost', 9515);
            if ($fp !== false) {
                fclose($fp);
                return true;
            }
        } catch (\Exception $e) {
        }
        return false;
    }

    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--window-size=1920,1080',
            '--remote-debugging-port=9222'
        ]);

        return RemoteWebDriver::create(
            'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY,
                $options
            )
        );
    }
}
