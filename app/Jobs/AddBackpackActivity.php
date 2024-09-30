<?php

namespace App\Jobs;

use App\Models\BackpackActivity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddBackpackActivity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected $parent, protected $children = [])
    {
        //
    }

    /**
     * @param $attribute
     * @return string
     */
    public function serialize($attribute): string
    {
        return is_string($attribute) ? $attribute : json_encode($attribute);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $parentEntity = $this->parent['entry']::class;
        BackpackActivity::whereEntity($parentEntity)
            ->where('column', '=', $this->parent['attribute'])
            ->whereEntityId($this->parent['entry']->id)
            ->update([
                'undo' => false,
            ]);
        $parentActivity = BackpackActivity::create([
            'session_id' => $this->parent['session_id'],
            'user_id' => $this->parent['user_id'],
            'entity_id' => $this->parent['entry']->id,
            'entity' => $parentEntity,
            'column' => $this->parent['attribute'],
            'old_value' => $this->parent['oldValue'],
            'new_value' => $this->parent['newValue'],
        ]);
        foreach ($this->children as $child) {
            BackpackActivity::create([
                'session_id' => $child['session_id'],
                'user_id' => $child['user_id'],
                'parent_id' => $parentActivity->id,
                'entity_id' => $child['entry']->id,
                'entity' => $child['entry']::class,
                'column' => $child['attribute'],
                'old_value' => $child['oldValue'],
                'new_value' => $this->serialize($child['entry']->{$child['attribute']}),
            ]);
        }
    }
}
