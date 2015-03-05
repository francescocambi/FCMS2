<?php
/**
 * User: Francesco
 * Date: 05/03/15
 * Time: 12:05
 */

namespace App\Admin;


class JSONMessageComposer implements MessageComposerInterface {

    /**
     * {@inheritdoc}
     * @return string
     */
    public function successMessage()
    {
        return json_encode(array(
            'status' => true
        ));
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function dataMessage($data)
    {
        return json_encode(array(
                'status' => true,
                'data' => $data
        ));
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function failureMessage($description)
    {
        return json_encode(array(
            'status' => false,
            'exception' => $description
        ));
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function exceptionMessage($exception)
    {
        return json_encode(array(
            'status' => false,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ));
    }

} 