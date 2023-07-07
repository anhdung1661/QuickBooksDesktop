<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\DB\Ddl\Trigger;
use Magento\Framework\DB\Ddl\TriggerFactory;
use Magento\Framework\Module\Setup;
use Magento\Framework\App\Bootstrap;

require __DIR__ . '/../../../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);
$obj = $bootstrap->getObjectManager();

$setup = $obj->get(Setup::class);

/**
 * Create table magenest_qbd__queue_log
 */
$tableName = $setup->getTable('magenest_qbd__queue_log');
$table = $setup->getConnection()->newTable(
        $tableName
    )->addColumn(
        'company_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'company_id'
    )->addColumn(
        'entity_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'entity_id'
    )->addColumn(
        'type',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'type'
    )->addColumn(
        'action',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'action'
    )->addColumn(
        'old_status',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'old_status'
    )->addColumn(
        'new_status',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        [],
        'new_status'
    )->addColumn(
        'msg',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        255,
        [],
        'msg'
    );
$setup->getConnection()->createTable($table);

/**
 * Create trigger
 */
foreach (Trigger::getListOfEvents() as $event) {
    $triggerName = 'magenest_qbd__queue_log' . $event;
    $trigger = $obj->get(TriggerFactory::class)->create()
        ->setName($triggerName)
        ->setTime(Trigger::TIME_AFTER)
        ->setEvent($event)
        ->setTable($setup->getTable('magenest_qbd__queue'));
    if ($event == Trigger::EVENT_DELETE) {
        $trigger->addStatement("INSERT IGNORE INTO {$tableName} (company_id, entity_id, type, action, old_status, new_status, msg)
                            VALUES (OLD.company_id, OLD.entity_id, OLD.type, OLD.action, OLD.status, OLD.status, '{$event}');");
    } elseif ($event == Trigger::EVENT_UPDATE) {
        $trigger->addStatement("INSERT IGNORE INTO {$tableName} (company_id, entity_id, type, action, old_status, new_status, msg)
                            VALUES (NEW.company_id, NEW.entity_id, NEW.type, NEW.action, OLD.status, NEW.status, '{$event}');");
    } else {
        $trigger->addStatement("INSERT IGNORE INTO {$tableName} (company_id, entity_id, type, action, old_status, new_status, msg)
                            VALUES (NEW.company_id, NEW.entity_id, NEW.type, NEW.action, NEW.status, NEW.status, '{$event}');");
    }
    $setup->getConnection()->dropTrigger($trigger->getName());
    $setup->getConnection()->createTrigger($trigger);
}
