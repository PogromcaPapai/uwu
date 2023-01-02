<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('app.agenda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg space-y-10">
                <div class="bg-white rounded shadow-lg">
                    <div class="px-6 py-4 space-y-3">
                        <table class="table-auto">
                            <thead>
                              <tr>
                                <th>id</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Set new password</th>
                                <th>Mod</th>
                                <th>Actions</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)    
                                <tr>
                                  <form action="admin/users" method="post">
                                      <td><input type="number" name="id" id="id-field" readonly value="{{$user->id}}"></td>
                                      <td><input type="text" name="name" id="name-field" required value="{{$user->email}}"></td>
                                      <td><input type="email" name="email" id="email-field" required value="{{$user->email}}"></td>
                                      <td><input type="text" name="password" id="pass-field"></td>
                                      <td><input type="checkbox" name="id_mod" id="mod-field" 
                                          @if ($user->is_mod==1)
                                              checked
                                          @endif
                                          ></td>
                                      <td>
                                          <button type="submit">save</button>
                                          <a href="admin/users/edit/{{$user->id}}/delete" target="_blank" rel="noopener noreferrer">del</a>
                                      </td>
                                  </form>
                                </tr>
                                @endforeach
                            </tbody>
                          </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
