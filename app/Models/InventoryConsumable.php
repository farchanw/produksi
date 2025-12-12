<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryConsumable extends Model
{
    use HasFactory;

    protected $table = 'inventory_consumables';
    protected $primaryKey = 'id';
    protected $fillable = ["sku","name"];
    protected $appends = ['btn_delete', 'btn_edit', 'btn_show', 'btn_inventory_consumable_history'];

    public function category()
    {
        return $this->belongsTo(InventoryConsumableCategory::class, 'category_id');
    }

    public function getBtnDeleteAttribute()
    {
        $html = "<button type='button' class='btn btn-outline-danger btn-sm radius-6' style='margin:1px;' data-bs-toggle='modal' data-bs-target='#modalDelete' onclick='setDelete(" . json_encode($this->id) . ")'>
                    <i class='ti ti-trash'></i>
                </button>";

        return $html;
    }
    

    public function getBtnEditAttribute()
    {
        $html = "<button type='button' class='btn btn-outline-secondary btn-sm radius-6' style='margin:1px;' data-bs-toggle='offcanvas'  data-bs-target='#drawerEdit' onclick='setEdit(" . json_encode($this->id) . ")'>
                    <i class='ti ti-pencil'></i>
                </button>";

        return $html;
    }


    public function getBtnShowAttribute()
    {
        $html = "<button type='button' class='btn btn-outline-secondary btn-sm radius-6' style='margin:1px;' onclick='setShowPreview(" . json_encode($this->id) . ")'>
                <i class='ti ti-eye'></i>
                </button>";
        return $html;
    }


    public function getBtnInventoryConsumableHistoryAttribute()
    {
        $url = route('inventory-consumable-movement.index', ['inventory_consumable_id' => $this->id]);
        $html = "<button type='button' class='btn btn-outline-success btn-sm radius-6' style='margin:1px;' onclick='window.location.href=&quot;{$url}&quot;' title='Lihat Riwayat'>
                    <i class='ti ti-history'></i>
                </button>";

        return $html;
    }
    

    public function getUpdatedAtAttribute($value)
    {
        return $value ? date("Y-m-d H:i:s", strtotime($value)) : "-";
    }


    public function getCreatedAtAttribute($value)
    {
        return $value ? date("Y-m-d H:i:s", strtotime($value)) : "-";
    }
}
