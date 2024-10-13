<?php
namespace App\Listeners;

use App\Events\TaskStatusUpdatedEvent;
use App\Models\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateDependentTasksListener
{
    /**
     * Handle the event.
     *
     * @param  TaskStatusUpdatedEvent  $event
     * @return void
     */
    public function handle(TaskStatusUpdatedEvent $event)
    {
        // الحصول على المهام المعتمدة على المهمة التي تم تحديثها
        $dependentTasks = Task::where('depends_on', $event->task->id)->get();

        foreach ($dependentTasks as $dependentTask) {
            if ($event->task->status == 'Completed') {
                // إذا كانت المهمة الأصلية مكتملة، نحدث حالة المهام المعتمدة
                $dependentTask->status = 'Open';
                $dependentTask->save();
            }
        }
    }
}
