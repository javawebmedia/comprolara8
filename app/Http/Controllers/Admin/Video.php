<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Image;

class Video extends Controller
{
    // Index
    public function index()
    {
        if(Session()->get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}
        $video   = DB::table('video')->orderBy('id_video','DESC')->get();

        $data = array(  'title'     => 'Data Video',
                        'video'  => $video,
                        'content'   => 'admin/video/index'
                    );
        return view('admin/layout/wrapper',$data);
    }

    // Edit
    public function edit($id_video)
    {
        if(Session()->get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}
        $video   = DB::table('video')->where('id_video',$id_video)->orderBy('urutan','ASC')->first();

        $data = array(  'title'     => 'Edit Data Video',
                        'video'     => $video,
                        'content'   => 'admin/video/edit'
                    );
        return view('admin/layout/wrapper',$data);
    }

    // Proses
    public function proses(Request $request)
    {
        $site   = DB::table('konfigurasi')->first();
        // PROSES HAPUS MULTIPLE
        if(isset($_POST['hapus'])) {
            $id_videonya       = $request->id_video;
            for($i=0; $i < sizeof($id_videonya);$i++) {
                DB::table('video')->where('id_video',$id_videonya[$i])->delete();
            }
            return redirect('admin/video')->with(['sukses' => 'Data telah dihapus']);
        // PROSES SETTING DRAFT
        }
    }

    // tambah
    public function tambah(Request $request)
    {
        if(Session()->get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}
        request()->validate([
                            'judul'     => 'required|unique:video',
                            'video'     => 'required|unique:video',
                            'gambar'    => 'file|image|mimes:jpeg,png,jpg|max:8024'
                            ]);
        // UPLOAD START
        $image                  = $request->file('gambar');
        if(!empty($image)) {
            $filenamewithextension  = $request->file('gambar')->getClientOriginalName();
            $filename               = pathinfo($filenamewithextension, PATHINFO_FILENAME);
            $input['nama_file']     = Str::slug($filename, '-').'-'.time().'.'.$image->getClientOriginalExtension();
            $destinationPath        = './assets/upload/image/thumbs/';
            $img = Image::make($image->getRealPath(),array(
                'width'     => 150,
                'height'    => 150,
                'grayscale' => false
            ));
            $img->save($destinationPath.'/'.$input['nama_file']);
            $destinationPath = './assets/upload/image/';
            $image->move($destinationPath, $input['nama_file']);
            // END UPLOAD
            // UPLOAD START       
            DB::table('video')->insert([
                'judul'         => $request->judul,
                'posisi'        => $request->posisi,
                'keterangan'    => $request->keterangan,
                'video'         => $request->video,
                'urutan'        => $request->urutan,
                'id_user'       => Session()->get('id_user'),
                'bahasa'        => $request->bahasa
            ]);
            return redirect('admin/video')->with(['sukses' => 'Data telah ditambah']);
        }else{
            // UPLOAD START       
            DB::table('video')->insert([
                'judul'         => $request->judul,
                'posisi'        => $request->posisi,
                'keterangan'    => $request->keterangan,
                'video'         => $request->video,
                'urutan'        => $request->urutan,
                'id_user'       => Session()->get('id_user'),
                'bahasa'        => $request->bahasa,
                'gambar'        => $input['nama_file'],
            ]);
            return redirect('admin/video')->with(['sukses' => 'Data telah ditambah']);
        }
    }

    // edit
    public function proses_edit(Request $request)
    {
        if(Session()->get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}
        request()->validate([
                           'judul'      => 'required',
                            'video'     => 'required','gambar'    => 'file|image|mimes:jpeg,png,jpg|max:8024'
                            ]);
        // UPLOAD START
        $image                  = $request->file('gambar');
        if(!empty($image)) {
            $filenamewithextension  = $request->file('gambar')->getClientOriginalName();
            $filename               = pathinfo($filenamewithextension, PATHINFO_FILENAME);
            $input['nama_file']     = Str::slug($filename, '-').'-'.time().'.'.$image->getClientOriginalExtension();
            $destinationPath        = './assets/upload/image/thumbs/';
            $img = Image::make($image->getRealPath(),array(
                'width'     => 150,
                'height'    => 150,
                'grayscale' => false
            ));
            $img->save($destinationPath.'/'.$input['nama_file']);
            $destinationPath = './assets/upload/image/';
            $image->move($destinationPath, $input['nama_file']);
            // END UPLOAD
            DB::table('video')->where('id_video',$request->id_video)->update([
                'judul'         => $request->judul,
                'posisi'        => $request->posisi,
                'keterangan'    => $request->keterangan,
                'video'         => $request->video,
                'urutan'        => $request->urutan,
                'id_user'       => Session()->get('id_user'),
                'bahasa'        => $request->bahasa,
                'gambar'            => $input['nama_file'],
            ]);
            return redirect('admin/video')->with(['sukses' => 'Data telah diupdate']);
        }else{
            DB::table('video')->where('id_video',$request->id_video)->update([
                'judul'         => $request->judul,
                'posisi'        => $request->posisi,
                'keterangan'    => $request->keterangan,
                'video'         => $request->video,
                'urutan'        => $request->urutan,
                'id_user'       => Session()->get('id_user'),
                'bahasa'        => $request->bahasa
            ]);
            return redirect('admin/video')->with(['sukses' => 'Data telah diupdate']);
        }
    }

    // Delete
    public function delete($id_video)
    {
        if(Session()->get('username')=="") { return redirect('login')->with(['warning' => 'Mohon maaf, Anda belum login']);}
        DB::table('video')->where('id_video',$id_video)->delete();
        return redirect('admin/video')->with(['sukses' => 'Data telah dihapus']);
    }
}
