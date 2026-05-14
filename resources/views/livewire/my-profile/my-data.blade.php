<div>
    @if ($serviceId)
    @switch($serviceId)
    @case('SER001')
    <livewire:teacher.profile.teacher-data :peopleId="$peopleId" />
    @break
    @case('SER002')
    <livewire:sltes.profile.sltes-data :peopleId="$peopleId" />
    @break
    @case('SER003')
    <livewire:sltas.profile.sltas-data :peopleId="$peopleId" />
    @break
    @case('SER004')
    <livewire:principal.profile.principal-data :peopleId="$peopleId" />
    @break
    @case('SER005')
    <livewire:sleas.profile.sleas-data :peopleId="$peopleId" />
    @break
    @case('SER006')
    <div></div>
    @break
    @case('SER007')
    <livewire:d-o-s.profile.d-o-s-data :peopleId="$peopleId" />
    @break
    @case('SER008')
    <livewire:m-s-o.profile.m-s-o-data :peopleId="$peopleId" />
    @break
    @default
    <p>Unknown</p>
    @endswitch
    @endif
</div>