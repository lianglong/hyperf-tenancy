<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Concerns;

trait DealsWithMigrations
{
    protected function getMigrationPaths()
    {
        if ($this->input->hasOption('path') && $this->input->getOption('path')) {
            return parent::getMigrationPaths();
        }

        return BASE_PATH.'/database/migrations/tenant';//database_path('migrations/tenant');
    }
}
