<?php
/*
 * Utils - simple class for us to unit test
 * Copyright (C) 2024 Daniel Kelley
 * (Utils.php)
 *
 * User: Aguilita
 * Date: 3/5/2024
 * Time: 7:47 PM
 */
namespace SampleSyncApp;

class Utils
{
    /**
     * toSyncString - formats a sync string.
     *
     * @param \DateTime $todayDt
     * @param \DateTime $endDt
     *
     * @return string
     */
    public function toSyncString(\DateTime $todayDt, \DateTime $endDt) : string {
        return sprintf('Synchronizing data between %s and %s.', $todayDt->format("Y-m-d") , $endDt->format("Y-m-d"));
    }
}