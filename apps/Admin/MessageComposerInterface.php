<?php
/**
 * User: Francesco
 * Date: 05/03/15
 * Time: 12:00
 */

namespace App\Admin;


interface MessageComposerInterface {

    /**
     * Returns a message that contains a positive reply
     * @return mixed
     */
    public function successMessage();

    /**
     * Returns a positive message with data attached to it
     * @param array|object $data Data to attach to message
     * @return mixed
     */
    public function dataMessage($data);

    /**
     * Returns a failure message with error description
     * @param string $description
     * @return mixed
     */
    public function failureMessage($description);

    /**
     * Retruns a failure message with exception details attached to it
     * @param \Exception $exception Exception to report in message
     * @return mixed
     */
    public function exceptionMessage($exception);

} 