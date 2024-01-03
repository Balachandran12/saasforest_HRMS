<?php
namespace App\Filament\Resources\TimesheetResource\Pages;
use App\Filament\Resources\Timesheet\TimesheetResource as TimesheetTimesheetResource;
use App\Filament\Resources\TimesheetResource;
use App\Models\Timesheet\Project;
use App\Models\Timesheet\Task;
use App\Models\Timesheet\Timesheet;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Actions\Action ;
use Livewire\Attributes\On;
class TaskStatus extends Page
{
    protected static string $resource = TimesheetTimesheetResource::class;
    protected static string $view = 'filament.resources.timesheet-resource.pages.task-status';

    public $clicktap='1';

    public $record;
    public $project_assined;
    public $planing;
    public $project_inpro;
    public $project_done;
    public $project_ready;
    public function mount(): void
    {
        static::authorizeResourceAccess();
        $this->callStatus($this->record);
        // 'assigned', 'inprogress', 'submitted'
    }

 public $card_id;
 public $store_id;
    #[On('post-created')]
    public function save($postId,$id){
        // dd($id,$postId);
        $this->store_id=$id;
        $this->card_id=$postId;
        if($this->store_id=='list2'){

            Timesheet::where('id', $this->card_id)->update(['status' => 'inprogress']);
        }
        elseif($this->store_id=='list3'){
            $notifi=Timesheet::where('id', $this->card_id)->get();
            $project_id=Timesheet::where('id', $this->card_id)->get();
            Timesheet::where('id', $this->card_id)->update(['status' => 'ready']);
            $asd=User::find($notifi[0]->created_by);
            $recipient = $asd;
            // dd($recipient);
            $recipient->notify(
                Notification::make()
                    ->title('Task move into Ready for QA')
                    ->actions([
                        Action::make('view')
                            ->button()->close()->url('/timesheet/timesheets/'.$project_id[0]->project_id.'/edit')
                            ->markAsRead(),

                    ])
                    ->toDatabase(),
            );
        }
        elseif($this->store_id=='list4'){
            Timesheet::where('id', $this->card_id)->update(['status' => 'done']);
        }
        elseif($this->store_id=='list1'){
            Timesheet::where('id', $this->card_id)->update(['status' => 'planing']);
        }elseif($this->store_id=='list0'){
            Timesheet::where('id', $this->card_id)->update(['status' => 'assigned']);
        }else{

        }
        $this->callStatus($this->record);
    }
    public $count_assined;
    public $count_planing;
    public $count_inpro;
    public $count_ready;
    public $count_done;
    public function callStatus($id){
        // dd(Timesheet::all());
        $this->project_assined=Timesheet::where('project_id',$id)->where('status','assigned')->with('task')->with('users.employee','createdBy')->get();
        $this->planing=Timesheet::where('project_id',$id)->where('status','planing')->with('task')->with('users.employee','createdBy')->get();
        $this->project_inpro=Timesheet::where('project_id',$id)->where('status','inprogress')->with('task')->with('users.employee','createdBy')->get();
        $this->project_done=Timesheet::where('project_id',$id)->where('status','done')->with('task')->with('users.employee','createdBy')->get();
        $this->project_ready=Timesheet::where('project_id',$id)->where('status','ready')->with('task')->with('users.employee','createdBy')->get();
        $this->StatusCount($id);
    }

    public function StatusCount($id){
        $this->count_assined=Timesheet::where('project_id',$id)->where('status','assigned')->with('task')->count();
        $this->count_planing=Timesheet::where('project_id',$id)->where('status','planing')->with('task')->count();
        $this->count_inpro=Timesheet::where('project_id',$id)->where('status','inprogress')->with('task')->count();
        $this->count_ready=Timesheet::where('project_id',$id)->where('status','ready')->with('task')->count();
        $this->count_done=Timesheet::where('project_id',$id)->where('status','done')->with('task')->count();
    }

    public $task_assiner;
    public $task_name;
    public $task_desc;
    public $project_name;
    public function open($task_id){
        // dd($task_id);
        $task_details=Task::find($task_id);
        // dd(Task::all());
        $task_ass=Timesheet::where('task_id',$task_id)->get();
        // dd($task_ass);
        $user=User::find($task_ass[0]->created_by);
        $project_n=Project::find($task_details->project_id);

        $this->project_name= $project_n->name;

        $this->task_assiner=$user->name;
        $this->task_name= $task_details->name;
        $this->task_desc=$task_details->description;
        $this->dispatch('open-modal', id: 'open');//open card details model
    }

    public function OpenTap($value){
        $this->clicktap=$value;
    }


}
