<?php

namespace Sadad\Drivers;

interface DriverInterface
{
    /**
     * @param $inputs
     *
     * @return array
     */
    public function request($inputs);

    /**
     * @param $inputs
     *
     * @return array
     */
    public function verify($inputs);

    /**
     * @param $inputs
     *
     * @return array
     */
    public function setAddress($inputs);
}
