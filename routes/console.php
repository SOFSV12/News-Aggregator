<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:fetch-articles-command')->everyTenSeconds();
