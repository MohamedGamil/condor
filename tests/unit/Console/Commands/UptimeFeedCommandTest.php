<?php

use App\Aspect;
use App\Console\Commands\UptimeFeedCommand;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Tester\CommandTester;

class UptimeFeedCommandTest extends TestCase
{
    use DatabaseTransactions;
    use CreateUser, CreateAccount, CreateBoard, CreateFeed;

    protected $command;

    protected $commandTester;

    public function setUp()
    {
        parent::setUp();

        $this->mockAPI();

        $application = new ConsoleApplication();

        $testedCommand = $this->app->make(UptimeFeedCommand::class);
        $testedCommand->setLaravel(app());
        $application->add($testedCommand);

        $this->command = $application->find('uptime:feed');

        $this->commandTester = new CommandTester($this->command);

        $this->scenario();
    }

    /** @test */
    public function it_runs_uptime_feeds()
    {
        $this->commandTester->execute([
            'command' => $this->command->getName(),
        ]);

        $this->assertRegExp('/Feeding uptime/', $this->commandTester->getDisplay());
    }

    protected function scenario()
    {
        $user = $this->createUser();

        $account = $this->createAccount();

        $user->accounts()->save($account);

        $board = $this->createBoard();

        $account->boards()->save($board);

        $aspect = Aspect::whereName('uptime')->first();

        $feed = $this->createFeed([
            'aspect_id' => $aspect->id,
            'name'      => 'test',
            'apikey'    => 'm000000000-000000000000000000000000', // Dummy API Key
            ]);

        $board->feeds()->save($feed);
    }

    protected function mockAPI()
    {
        $this->app->bind('UptimeRobot', function () {
            $mock = Mockery::mock(Alariva\UptimeRobot\UptimeRobot::class)->makePartial();

            $mock->shouldReceive('getMonitors')
                 ->once()
                 ->with(0000)
                 ->andReturn($this->getStubResponse());

            return $mock;
        });
    }

    protected function getStubResponse()
    {
        return unserialize(base64_decode(
            'Tzo4OiJzdGRDbGFzcyI6NTp7czo0OiJzdGF0IjtzOjI6Im9rIjtzOjY6Im9mZnNldCI7czoxOiIw
            IjtzOjU6ImxpbWl0IjtpOjUwO3M6NToidG90YWwiO3M6MToiMSI7czo4OiJtb25pdG9ycyI7Tzo4
            OiJzdGRDbGFzcyI6MTp7czo3OiJtb25pdG9yIjthOjE6e2k6MDtPOjg6InN0ZENsYXNzIjoxMzp7
            czoyOiJpZCI7czo5OiI3NzY1NDI5ODQiO3M6MTI6ImZyaWVuZGx5bmFtZSI7czo4OiJ3d3cuZGt2
            bSI7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9ka3ZtLmNvbS5hci9ibG9nL2VzLyI7czo0OiJ0eXBl
            IjtzOjE6IjIiO3M6Nzoic3VidHlwZSI7czowOiIiO3M6MTE6ImtleXdvcmR0eXBlIjtzOjE6IjIi
            O3M6MTI6ImtleXdvcmR2YWx1ZSI7czo2OiJrYXJhdGUiO3M6MTI6Imh0dHB1c2VybmFtZSI7czow
            OiIiO3M6MTI6Imh0dHBwYXNzd29yZCI7czowOiIiO3M6NDoicG9ydCI7czowOiIiO3M6ODoiaW50
            ZXJ2YWwiO3M6NDoiMzYwMCI7czo2OiJzdGF0dXMiO3M6MToiMiI7czoxODoiYWxsdGltZXVwdGlt
            ZXJhdGlvIjtzOjU6Ijk5Ljk5Ijt9fX19'));
    }
}