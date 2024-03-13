<?php

namespace App\Livewire;

use App\Models\Todo;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Illuminate\Http\Request;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

#[Rule('required|min:3|max:255')]
public $name;
    public $search;
    public $editingTodoID;
    #[Rule('required|min:3|max:255')]
    public $editingTodoName;

    public function delete($todoID){

        try{
            Todo::findOrFail($todoID)->delete();
        } catch(\Exception $e) {
            session()->flash('error','Failed to delete todo');

        }
     }

    public function cancel(){
  $this->reset('editingTodoID','editingTodoName');
}
public function update(){
    $validated = $this->validate(['editingTodoName'=> 'required|min:3|max:255']);
    Todo::find($this->editingTodoID)->update(
        ['name'=> $this->editingTodoName]

    );
    $this->cancel();
}
    public function edit(Todo $todo){
       // $this->editingTodoID = $todoID;
       // $this->editingTodoName=Todo::find($todoID)->name;
      $this->editingTodoID = $todo->id;
      $this->editingTodoName=$todo->name;

    }
    public function toggle($todoID){
        $todo = Todo::find($todoID);
        $todo->completed = !$todo->completed;
        $todo->save();
    }
    public function create(){
        $validated = $this->validate(['name'=>'required|min:3|max:255']);
        $todos=Todo::create($validated);
        $this->reset('name');
        session()->flash('success','Created');
        return redirect('/');

    }
    public function render()
    {
        $todos=['todos'=>Todo::latest()->where('name','like',"%{$this->search}%")->paginate(3)];
        return view('livewire.todo-list',$todos);
    }
}
