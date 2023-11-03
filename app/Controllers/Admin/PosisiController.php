<?php

namespace App\Controllers\Admin;

use App\Models\PosisiModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\RESTful\ResourceController;

class PosisiController extends ResourceController
{
    protected PosisiModel $posisiModel;

    public function __construct()
    {
        $this->posisiModel = new PosisiModel();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $jabatanController = new JabatanController();
        return $jabatanController->index();
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $result = $this->posisiModel->findAll();

        $data = [
            'data' => $result,
            'empty' => empty($result)
        ];

        return view('admin/posisi/list_posisi', $data);
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        $data = [
            'ctx' => 'jabatan',
            'title' => 'Tambah Data Posisi',
        ];
        return view('/admin/posisi/create', $data);
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        if (!$this->validate([
            'posisi' => [
                'rules' => 'required|max_length[32]|is_unique[tb_posisi.posisi]',
            ],
        ])) {
            $data = [
                'ctx' => 'jabatan',
                'title' => 'Tambah Data Jabatan',
                'validation' => $this->validator,
                'oldInput' => $this->request->getVar()
            ];
            return view('/admin/posisi/new', $data);
        }

        // ambil variabel POST
        $posisi = $this->request->getVar('posisi');

        $result = $this->posisiModel->insert(['posisi' => $posisi]);

        if ($result) {
            session()->setFlashdata([
                'msg' => 'Tambah data berhasil',
                'error' => false
            ]);
            return redirect()->to('/admin/posisi');
        }

        session()->setFlashdata([
            'msg' => 'Gagal menambah data',
            'error' => true
        ]);
        return redirect()->to('/admin/posisi/new');
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        $posisi = $this->posisiModel->where(['id' => $id])->first();

        if (!$posisi) {
            throw new PageNotFoundException('Data posisi dengan id ' . $id . ' tidak ditemukan');
        }

        $data = [
            'ctx' => 'jabatan',
            'data' => $posisi,
            'title' => 'Edit Posisi',
        ];
        return view('/admin/posisi/edit', $data);
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $posisi = $this->posisiModel->where(['id' => $id])->first();

        // ambil variabel POST
        $namaPosisi = $this->request->getRawInputVar('posisi');

        if ($posisi['posisi'] != $namaPosisi && !$this->validate([
            'posisi' => [
                'rules' => 'required|max_length[32]|is_unique[tb_posisi.posisi]',
            ],
        ])) {
            if (!$posisi) {
                throw new PageNotFoundException('Data posisi dengan id ' . $id . ' tidak ditemukan');
            }

            $data = [
                'ctx' => 'jabatan',
                'title' => 'Edit Posisi',
                'data' => $posisi,
                'validation' => $this->validator,
                'oldInput' => $this->request->getRawInput()
            ];
            return view('/admin/posisi/edit', $data);
        }

        $result = $this->posisiModel->update($id, [
            'posisi' => $namaPosisi
        ]);

        if ($result) {
            session()->setFlashdata([
                'msg' => 'Edit data berhasil',
                'error' => false
            ]);
            return redirect()->to('/admin/posisi');
        }

        session()->setFlashdata([
            'msg' => 'Gagal mengubah data',
            'error' => true
        ]);
        return redirect()->to('/admin/posisi/' . $id . '/edit');
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $result = $this->posisiModel->delete($id);

        if ($result) {
            session()->setFlashdata([
                'msg' => 'Data berhasil dihapus',
                'error' => false
            ]);
            return redirect()->to('/admin/posisi');
        }

        session()->setFlashdata([
            'msg' => 'Gagal menghapus data',
            'error' => true
        ]);
        return redirect()->to('/admin/posisi');
    }
}
