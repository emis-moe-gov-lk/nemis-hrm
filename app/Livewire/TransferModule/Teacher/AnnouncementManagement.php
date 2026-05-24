<?php

namespace App\Livewire\TransferModule\Teacher;

use App\Models\TransferAnnouncement;
use App\Support\Transfer\TransferAccess;
use Livewire\Component;
use Livewire\WithPagination;

class AnnouncementManagement extends Component
{
    use WithPagination;

    public bool $canManageAnnouncements = false;

    public $title;
    public $content;
    public $type = 'info';
    public $is_active = true;
    public $publish_date;
    public $expiry_date;
    public $link_text;
    public $link_route;
    public $display_order = 0;

    public $editingAnnouncement = null;
    public $showForm = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'type' => 'required|in:info,warning,danger,success',
        'is_active' => 'boolean',
        'publish_date' => 'nullable|date',
        'expiry_date' => 'nullable|date|after_or_equal:publish_date',
        'link_text' => 'nullable|string|max:255',
        'link_route' => 'nullable|string|max:255',
        'display_order' => 'integer',
    ];

    public function mount()
    {
        abort_unless(TransferAccess::canViewPortal(auth()->user()), 403);

        $this->canManageAnnouncements = TransferAccess::canManageAnnouncements(auth()->user());

        $this->publish_date = now()->format('Y-m-d\TH:i');
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetFields();
        }
    }

    public function resetFields()
    {
        $this->reset(['title', 'content', 'type', 'is_active', 'publish_date', 'expiry_date', 'link_text', 'link_route', 'display_order', 'editingAnnouncement']);
        $this->publish_date = now()->format('Y-m-d\TH:i');
        $this->type = 'info';
        $this->is_active = true;
    }

    public function save()
    {
        abort_unless(TransferAccess::canManageAnnouncements(auth()->user()), 403);

        $data = $this->validate();

        if ($this->editingAnnouncement) {
            $this->editingAnnouncement->update($data);
            session()->flash('success', 'Announcement updated successfully.');
        } else {
            TransferAnnouncement::create($data);
            session()->flash('success', 'Announcement created successfully.');
        }

        $this->resetFields();
        $this->showForm = false;
    }

    public function edit(TransferAnnouncement $announcement)
    {
        abort_unless(TransferAccess::canManageAnnouncements(auth()->user()), 403);

        $this->editingAnnouncement = $announcement;
        $this->title = $announcement->title;
        $this->content = $announcement->content;
        $this->type = $announcement->type;
        $this->is_active = $announcement->is_active;
        $this->publish_date = $announcement->publish_date ? $announcement->publish_date->format('Y-m-d\TH:i') : null;
        $this->expiry_date = $announcement->expiry_date ? $announcement->expiry_date->format('Y-m-d\TH:i') : null;
        $this->link_text = $announcement->link_text;
        $this->link_route = $announcement->link_route;
        $this->display_order = $announcement->display_order;
        $this->showForm = true;
    }

    public function delete(TransferAnnouncement $announcement)
    {
        abort_unless(TransferAccess::canManageAnnouncements(auth()->user()), 403);

        $announcement->delete();
        session()->flash('success', 'Announcement deleted successfully.');
    }

    public function render()
    {
        return view('livewire.transfer-module.teacher.announcement-management', [
            'announcements' => TransferAnnouncement::orderBy('display_order', 'asc')
                ->orderBy('created_at', 'desc')
                ->paginate(10),
            'canManageAnnouncements' => $this->canManageAnnouncements,
        ]);
    }
}
