<div class="table-responsive">
    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th>Fitur</th>
                @foreach($allActions as $action)
                    <th class="text-center">{{ ucfirst($action) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($features as $feature)
                @php
                    $permissions = $permissionsGrouped[$feature] ?? collect();
                @endphp
                <tr>
                    <td>{{ ucfirst(str_replace('_', ' ', $feature)) }}</td>
                    @foreach($allActions as $action)
                        @php
                            $perm = $permissions->firstWhere('action', $action);
                        @endphp
                        <td class="text-center">
                            @if($perm)
                                <input type="checkbox" 
                                    name="permissions[]" 
                                    value="{{ $perm->id }}" 
                                    id="permission_{{ $perm->id }}">
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
