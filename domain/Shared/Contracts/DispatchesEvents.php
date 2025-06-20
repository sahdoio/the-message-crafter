<?php

namespace Domain\Shared\Contracts;

interface DispatchesEvents
{
    /**
     * @return object[] domain events to be dispatched
     */
    public function pullDomainEvents(): array;
}
