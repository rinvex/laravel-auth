<?php

declare(strict_types=1);

namespace Rinvex\Fort\Traits;

use Rinvex\Fort\Models\Ability;

trait HasAbilities
{
    /**
     * Attach the given abilities to the model.
     *
     * @param string|array $action
     * @param string|array $resource
     *
     * @return $this
     */
    public function grantAbilities($action, $resource)
    {
        $this->setAbilities($action, $resource, 'syncWithoutDetaching');

        return $this;
    }

    /**
     * Sync the given abilities to the model.
     *
     * @param string|array $action
     * @param string|array $resource
     *
     * @return $this
     */
    public function syncAbilities($action, $resource)
    {
        $this->setAbilities($action, $resource, 'sync');

        return $this;
    }

    /**
     * Detach the given abilities from the model.
     *
     * @param string|array $action
     * @param string|array $resource
     *
     * @return $this
     */
    public function revokeAbilities($action, $resource)
    {
        $this->setAbilities($action, $resource, 'detach');

        return $this;
    }

    /**
     * Set the given ability(s) to the model.
     *
     * @param string|array $action
     * @param string|array $resource
     * @param string       $process
     *
     * @return bool
     */
    protected function setAbilities($action, $resource, string $process)
    {
        // Guess event name
        $event = $process === 'syncWithoutDetaching' ? 'attach' : $process;

        // If the "attaching/syncing/detaching" event returns false we'll cancel this operation and
        // return false, indicating that the attaching/syncing/detaching failed. This provides a
        // chance for any listeners to cancel save operations if validations fail or whatever.
        if ($this->fireModelEvent($event.'ing') === false) {
            return false;
        }

        // Ability model
        $model = Ability::query();

        if (is_string($action) && $action !== '*') {
            $model->where('action', $action);
        }

        if (is_array($action)) {
            $model->whereIn('action', $action);
        }

        if (is_string($resource) && $resource !== '*') {
            $model->where('resource', $resource);
        }

        if (is_array($resource)) {
            $model->whereIn('resource', $resource);
        }

        // Find the given abilities
        $abilities = $model->get();

        // Sync abilities
        $this->abilities()->$process($abilities);

        // Fire the roles attached/synced/detached event
        $this->fireModelEvent($event.'ed', false);

        return true;
    }
}
