<?php

namespace SXFRating2;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;

class Setup extends AbstractSetup
{
	public function install()
    {
		$sm = $this->schemaManager();
		
        $sm->alterTable('xf_sxfr_rating', function(Alter $table)
        {
            $table->addColumn('rating_id', 'VARBINARY', 50);
            $table->addColumn('title', 'VARCHAR', 50);
            $table->addColumn('icon', 'VARCHAR', 100);
			$table->addColumn('callback', 'VARCHAR', 100);
			$table->addColumn('active', 'TINYINT')->setDefault(1);
        });
		
		$sm->query("
			INSERT INTO `xf_sxfr_rating` 
				(`rating_id`, `title`, `icon`, `callback`, `active`) 
			VALUES
				(0x757365726c696b6573, 'Top for likes', 'fa-star-half-o', 'SXFRating2\\Rating\\UserLike', 1),
				(0x757365726d65737361676573, 'Top for messages', 'fa-comments-o', 'SXFRating2\\Rating\\UserMessage', 1),
				(0x75736572706f696e7473, 'Top fot points', 'fa-trophy', 'SXFRating2\\Rating\\UserPoint', 1),
				(0x757365727265736f75726365, 'Top for resources', 'fa-database', 'SXFRating2\\Rating\\UserResource', 1);
		");
    }

    public function upgrade()
    {

    }

    public function uninstall()
    {
        $this->schemaManager()->dropTable('xf_sxfr_rating');
    }
}