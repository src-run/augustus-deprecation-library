<?php
/**
 * Created by PhpStorm.
 * User: rmf
 * Date: 4/24/16
 * Time: 8:46 AM
 */
namespace SR\Deprecation;

use Psr\Log\LoggerInterface;
use SR\Deprecation\Actor\NotifierInterface;
use SR\Deprecation\Model\Notice;


/**
 * Class Deprecation.
 */
interface DeprecationInterface
{
    /**
     * @param null|LoggerInterface   $logger
     * @param null|NotifierInterface $notifier
     *
     * @return null
     */
    public static function enable(LoggerInterface $logger = null, NotifierInterface $notifier = null);

    /**
     * @param string|null $mode
     *
     * @return string
     */
    public static function mode(string $mode = null) : string;

    /**
     * @param Notice $notice
     *
     * @return null
     */
    public static function definition(Notice $notice);

    /**
     * @param Notice|null $notice
     *
     * @return null
     */
    public static function invoke(Notice $notice = null);
}