<?php

namespace App\Livewire;

use App\Models\ProductDiscounts;
use App\Models\Setting;
use App\Models\StokBarang;
use Livewire\Component;

class SettingsIndex extends Component
{
    public $company_name = '';
    public $address = '';
    public $phone = '';
    public $footer_text = '';
    public $member_discount = 0;
    public $non_member_discount = 0;
    public $stok_barang_id = '';
    public $harga_diskon = 0;

    public function mount()
    {
        $this->company_name        = Setting::getValue('company_name', '');
        $this->address             = Setting::getValue('address', '');
        $this->phone               = Setting::getValue('phone', '');
        $this->footer_text         = Setting::getValue('footer_text', '');
        $this->member_discount     = Setting::getValue('member_discount', 0);
        $this->non_member_discount = Setting::getValue('non_member_discount', 0);
    }

    public function updateSettings()
    {
        $this->validate([
            'company_name'        => 'nullable|string|max:255',
            'address'             => 'nullable|string|max:255',
            'phone'               => 'nullable|string|max:20',
            'footer_text'         => 'nullable|string|max:255',
            'member_discount'     => 'nullable|numeric|min:0|max:100',
            'non_member_discount' => 'nullable|numeric|min:0|max:100',
        ]);

        $fields = ['company_name', 'address', 'phone', 'footer_text', 'member_discount', 'non_member_discount'];

        foreach ($fields as $field) {
            Setting::updateOrCreate(['key' => $field], ['value' => $this->$field ?? '']);
        }

        $this->dispatch('notify', 'Pengaturan berhasil diperbarui.', 'success');
    }

    public function addProductDiscount()
    {
        $this->validate([
            'stok_barang_id' => 'required|exists:stok_barang,id',
            'harga_diskon'   => 'required|numeric|min:0|max:100',
        ]);

        ProductDiscounts::updateOrCreate(
            ['stok_barang_id' => $this->stok_barang_id],
            ['harga_diskon' => $this->harga_diskon, 'is_active' => true]
        );

        $this->reset(['stok_barang_id', 'harga_diskon']);
        $this->resetValidation();
        $this->dispatch('notify', 'Diskon produk berhasil ditambahkan.', 'success');
    }

    public function removeProductDiscount($id)
    {
        ProductDiscounts::findOrFail($id)->delete();
        $this->dispatch('notify', 'Diskon produk berhasil dihapus.', 'success');
    }

    public function render()
    {
        return view('components.settings-index', [
            'discountedProducts' => ProductDiscounts::with('stokBarang')->get(),
            'products'           => StokBarang::orderBy('nama_barang')->get(),
        ]);
    }
}