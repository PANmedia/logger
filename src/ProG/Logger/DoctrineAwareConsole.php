<?php

namespace ProG\Logger;

use Doctrine\DBAL\Logging\SQLLogger;

class DoctrineAwareConsole extends Console implements SQLLogger
{

}
