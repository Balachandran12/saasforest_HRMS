<?php

namespace App\Filament\Resources\Timesheet\ProjectResource\RelationManagers;

use App\Models\Employee\Employee;
use App\Models\Timesheet\Project;
use Filament\Forms;
use App\Models\Timesheet\Task;
use App\Models\Timesheet\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Actions\Action;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $recordTitleAttribute = 'task_id';


    public  function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)->disabled(function(){
                        if(!auth()->user()->hasRole('Supervisor')){
                            return true;
                        }
                    }),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull()->disabled(function(){
                        if(!auth()->user()->hasRole('Supervisor')){
                            return true;
                        }
                    }),
                    Select::make('author_id')
                    ->label('Assinee')
                    ->options(function(){
                        $owner=$this->getOwnerRecord();
                        $user=Project::where('team_id',$owner->team_id)->pluck('user_id');
                        $team_user=[];
                        foreach($user as $u){
                            $w[]=$u;
                        }
                        return User::whereIn('id',$team_user)->pluck('name','id');
                    })
                    ->searchable(),

                Forms\Components\DatePicker::make('end_date')
                ->native(false)
                ->suffixIcon('heroicon-m-calendar')
                ->minDate(function (Get $get) {
                    $current_project=$this->getOwnerRecord();
                    // dd($v);
                    $StartDate = $current_project->start_date;
                    if($StartDate!=null){
                         return $StartDate ? Carbon::parse($StartDate) : now();
                    }
                    else{
                        $timesheet = Project::Find($get('project'));
                        // dd( $timesheet);
                    if($timesheet!=null){
                        return $timesheet->start_date;
                    }
                    }
                })
                ->maxDate(function (Get $get) {
                    // $timesheet = project::Find($get('project'));
                    $current_project=$this->getOwnerRecord();
                    if($current_project->end_date!=null){
                        return $current_project->end_date;
                    }
                })
                    ->label('End date')->required(),



            ]);
    }

    public  function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('description'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->after(function($data){
                    $r=$this->getOwnerRecord();
                    $lastRecordId = Task::where('project_id',$r->id)
                        ->latest('id')
                        ->value('id');
                        $user=User::find($data['author_id']);
                     Timesheet::create([
                        'user_id'=>$data['author_id'],
                        'task_id'=>$lastRecordId,
                        'project_id'=>$r->id,
                        'end_data'=>$data['end_date'],
                     ]);
                     $user->notify(
                        Notification::make()
                            ->title('Task Assined For You')
                            ->actions([
                                Action::make('view')
                                    ->button()->url('/timesheet/timesheets/'.$r->id.'/edit'),

                            ])
                            ->toDatabase(),
                    );
                })
                ->visible(function(){
                    if(auth()->user()->hasRole('Supervisor')){
                        return true;
                    }
                }),
            //     ->after(function($record){
            //        return redirect()->to('/timesheet/projects');
            // }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(function(){
                    if(auth()->user()->hasRole('Supervisor')){
                        return true;
                    }
                }),
                // ->url(fn (Task $record): string => route('filament.admin.resources.timesheet.tasks.edit', $record)),
                Tables\Actions\DeleteAction::make() ->visible(function(){
                    if(auth()->user()->hasRole('Supervisor')){
                        return true;
                    }
                }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make() ->visible(function(){
                    if(auth()->user()->hasRole('Supervisor')){
                        return true;
                    }
                }),
            ]);
    }
}
