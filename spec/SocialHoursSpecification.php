<?php
namespace spec\groupcash\socialhours;

use groupcash\php\algorithms\FakeAlgorithm;
use groupcash\socialhours\app\Launcher;
use groupcash\socialhours\app\Session;
use groupcash\socialhours\model\PostOffice;
use groupcash\socialhours\model\Time;
use rtens\domin\delivery\web\Url;
use rtens\mockster\Mockster;
use watoki\karma\stores\EventStore;
use watoki\karma\testing\Specification;

class SocialHoursSpecification extends Specification {

    /** @var FakeAlgorithm */
    protected $algorithm;
    /** @var PostOffice */
    protected $postOffice;

    public function __construct() {
        $this->algorithm = new FakeAlgorithm();
        $this->postOffice = Mockster::of(PostOffice::class);

        Time::freeze();

        parent::__construct(function (EventStore $store) {
            /** @var PostOffice $postOffice */
            $postOffice = Mockster::mock($this->postOffice);
            return (new Launcher($store, $this->algorithm, $postOffice, Mockster::mock(Session::class), new Url('http', 'example.com')))->application;
        });
    }
}