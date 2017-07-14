<?php

namespace Laravel\Horizon\Tests\Feature\Fakes;

use Exception;
use Laravel\Horizon\Supervisor;

class SupervisorThatThrowsException extends Supervisor
{
    /**
     * Persist information about this supervisor instance.
     *
     * @return void
     */
    public function persist()
    {
        throw new Exception;
    }
}
