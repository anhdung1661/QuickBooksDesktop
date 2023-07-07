<?php
declare(strict_types=1);

/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Booking & Reservation extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 19/01/2021 16:57
 */

namespace Magenest\QuickBooksDesktop\Console\Command;

use Magenest\QuickBooksDesktop\Helper\QueueAction;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InsertAllCustomer
 * @package Magenest\QuickBooksDesktop\Console\Command
 */
class InsertAllCustomer extends Command
{
    /**
     * @var QueueAction
     */
    protected $_queueAction;

    /**
     * InsertAllProduct constructor.
     * @param QueueAction $queueAction
     * @param string|null $name
     */
    public function __construct(
        QueueAction $queueAction,
        string $name = null
    ) {
        parent::__construct($name);
        $this->_queueAction = $queueAction;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('qbd:insert:customer');
        $this->setDescription('Insert all customers to Queue');

        parent::configure();
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @return int|null null or 0 if everything went fine, or an error code
     *
     * @throws LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            list($numberAdded, $numberNotAdded) = $this->_queueAction->addCustomerToQueue();
        } catch (LocalizedException $e) {
            throw new \LogicException($e->getMessage());
        }

        $output->writeln('<info>' . $numberAdded . ' customers has been added to Queue.</info>');
        $output->writeln('<info>' . $numberNotAdded . ' customers hasn\'t been added to Queue.</info>');

        return 0;
    }
}