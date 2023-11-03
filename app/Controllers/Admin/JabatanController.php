<?php

namespace App\Controllers\Admin;

use App\Models\PosisiModel;
use App\Models\JabatanModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\RESTful\ResourceController;

class JabatanController extends ResourceController
{
    protected JabatanModel $jabatanModel;

    protected PosisiModel $posisiModel;

    public function __construct()
    {
        $this->jabatanModel = new JabatanModel();
        $this->posisiModel = new PosisiModel();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $data = [
            'title' => 'Jabatan & Posisi',
            'ctx' => 'jabatan',
        ];

        return view('admin/jabatan/index', $data);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $result = $this->jabatanModel
            ->join('tb_posisi', 'tb_jabatan.id_posisi = tb_posisi.id', 'LEFT')
            ->findAll();

        $data = [
            'data' => $result,
            'empty' => empty($result)
        ];

        return view('admin/jabatan/list_jabatan', $data);
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        $posisi = $this->posisiModel->findAll();

        $data = [
            'ctx' => 'jabatan',
            'jurusan' => $posisi,
            'title' => 'Tambah Data Jabatan',
        ];
        return view('/admin/jabatan/create', $data);
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        if (!$this->validate([
            'jabatan' => [
                'rules' => 'required|max_length[32]',
            ],
            'idPosisi' => [
                'rules' => 'required|numeric',
            ],
        ])) {
            $posisi = $this->posisiModel->findAll();

            $data = [
                'ctx' => 'jabatan',
                'posisi' => $posisi,
                'title' => 'Tambah Data Jabatan',
                'validation' => $this->validator,
                'oldInput' => $this->request->getVar()
            ];
            return view('/admin/posisi/new', $data);
        }

        // ambil variabel POST
        $jabatan = $this->request->getVar('jabatan');
        $idPosisi = $this->request->getVar('idPosisi');

        $result = $this->jabatanModel->tambahJabatan($jabatan, $idPosisi);

        if ($result) {
            session()->setFlashdata([
                'msg' => 'Tambah data berhasil',
                'error' => false
            ]);
            return redirect()->to('/admin/jabatan');
        }

        session()->setFlashdata([
            'msg' => 'Gagal menambah data',
            'error' => true
        ]);
        return redirect()->to('/admin/jabatan/new');
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        $jabatan = $this->jabatanModel->where(['id_jabatan' => $id])->first();

        if (!$jabatan) {
            throw new PageNotFoundException('Data jabatan dengan id ' . $id . ' tidak ditemukan');
        }

        $posisi = $this->posisiModel->findAll();

        $data = [
            'ctx' => 'jabatan',
            'data' => $jabatan,
            'posisi' => $posisi,
            'title' => 'Edit Jabatan',
        ];
        return view('/admin/jabatan/edit', $data);
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        if (!$this->validate([
            'jabatan' => [
                'rules' => 'required|max_length[32]',
            ],
            'idPosisi' => [
                'rules' => 'required|numeric',
            ],
        ])) {
            $posisi = $this->posisiModel->findAll();

            $jabatan = $this->jabatanModel->where(['id_jabatan' => $id])->first();

            if (!$jabatan) {
                throw new PageNotFoundException('Data jabatan dengan id ' . $id . ' tidak ditemukan');
            }

            $data = [
                'ctx' => 'jabatan',
                'posisi' => $posisi,
                'title' => 'Edit jabatan',
                'data' => $jabatan,
                'validation' => $this->validator,
                'oldInput' => $this->request->getRawInput()
            ];
            return view('/admin/jabatan/edit', $data);
        }

        // ambil variabel POST
        $jabatan = $this->request->getRawInputVar('jabatan');
        $idPosisi = $this->request->getRawInputVar('idPosisi');

        $result = $this->jabatanModel->update($id, [
            'jabatan' => $jabatan,
            'id_posisi' => $idPosisi
        ]);

        if ($result) {
            session()->setFlashdata([
                'msg' => 'Edit data berhasil',
                'error' => false
            ]);
            return redirect()->to('/admin/jabatan');
        }

        session()->setFlashdata([
            'msg' => 'Gagal mengubah data',
            'error' => true
        ]);
        return redirect()->to('/admin/jabatan/' . $id . '/edit');
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $result = $this->jabatanModel->delete($id);

        if ($result) {
            session()->setFlashdata([
                'msg' => 'Data berhasil dihapus',
                'error' => false
            ]);
            return redirect()->to('/admin/jabatan');
        }

        session()->setFlashdata([
            'msg' => 'Gagal menghapus data',
            'error' => true
        ]);
        return redirect()->to('/admin/jabatan');
    }
}
