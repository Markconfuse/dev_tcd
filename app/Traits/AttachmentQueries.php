<?php

namespace App\Traits;

use DB;
use App;
use File;
use Config;
use Storage;
use Session;
use DateTime;
use Response;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;


use App\Attachment;
use App\AttachmentStream;

trait AttachmentQueries
{
    
    public function insertAttachment($request)
    {
      // dd($request);

       $stream_id = "";
       $files = Storage::files('\public\TempFolders\\'.$request->unique);
        // dd($files);

       $fileStreamPath = '\\\192.168.0.150\\appsdevserver\\TCDFiles2k22\\TCDFiles';
       $i = 0;

        // if ($request->ticketID == 24934) {
        //   dd($files);
        //   // code...
        // }

        if (!empty($files)) {
            foreach($files as $key => $file)
            {

              // if ($request->ticketID == 24934) {
              //   dd($file,File::copy(storage_path('app\\').$file, $fileStreamPath.'\\'.basename($file)));
              //   // code...
              // }

                if(Session('userData')->Email == 'laranda@ics.com.ph') {
                    File::copy(storage_path('app\\').$file, storage_path('app\ESDAttachments').'\\'.basename($file)); 
                    File::copy(storage_path('app\\').$file, $fileStreamPath.'\\'.basename($file)); 

                    $request->request->add(['streamID' => $this->getFileStreamID(basename($file))]);

                    $fileID = $this->insertAttachDetails($request);
                } else {
                    if(@File::copy(storage_path('app\\').$file, $fileStreamPath.'\\'.basename($file)) === true) {
                        File::copy(storage_path('app\\').$file, storage_path('app\ESDAttachments').'\\'.basename($file)); 
                        File::copy(storage_path('app\\').$file, $fileStreamPath.'\\'.basename($file)); 

                        $request->request->add(['streamID' => $this->getFileStreamID(basename($file))]);

                        $fileID = $this->insertAttachDetails($request);
                    }
                }

            }
            
            Storage::deleteDirectory('/public/TempFolders/'.$request->unique);
        }
    }

    public function insertAttachDetails($request)
    {

        $insertAttachDetails = new Attachment();

        $insertAttachDetails->stream_id = $request->streamID;
        $insertAttachDetails->ticket_id = $request->ticketID;
        if(!empty($request->replyID)) {
           $insertAttachDetails->reply_id = $request->replyID;
        } 
        
        $insertAttachDetails->save();

        return $insertAttachDetails->fileID;
    }

    public function getFileStreamID($filename)
    {

        return AttachmentStream::WHERE('name', '=', $filename)->pluck('stream_id')[0];

    }

    public function cleanSNote($content)
    {
        if(!empty($content)) {
            $dom = new \DomDocument();
            libxml_use_internal_errors(true);

            $dom->loadHtml('<?xml encoding="UTF-8">'.trim($content));    
            $images = $dom->getElementsByTagName('img');

            $domain_path = 'https://tcd-portal.ics.com.ph/';

            foreach($images as $k => $img) {
                $data = $img->getAttribute('src');
                // dd($content);
                
                if(Str::contains($data, 'base64')) {
                    // list($type, $data) = explode(';', $data);// ------ 08/26/2022 omitted by dramos >> this uploads corrupt images when using summernotes insert image and drag/drop image
                    // list(, $data)      = explode(',', $data);// ------ 08/26/2022 omitted by dramos >> this uploads corrupt images when using summernotes insert image and drag/drop image
                } else {
                    $data = str_replace('amp;', '', $data);

                    // if(@file_get_contents($data) === true) { // ------ 08/24/2022 omitted by dramos >> this uploads corrupt images when pasting pics in summernotes
                    //     $data = 'data:image/jpg;base64,'.base64_encode(file_get_contents($data));
                    //     $data = file_get_contents($data);
                    // } 

                }

                            if (Str::contains($content, 'it_appsdev_test')) {
                              // $data = base64_encode($data);
                              // dd($data,($this->isBase64Encoded($data) ===false),base64_decode($data));
                              // $data = 'data:image/jpg;base64,'.base64_encode(file_get_contents($data));
                              // dd(file_get_contents($data) === false,$data);
                              // $data = file_get_contents($data);

                              // // $data = base64_decode($data);
                              // $path_fname = 'public/img/summernote_img/'.Str::random(5).Carbon::now()->format('mys').$k.'.png';
                              // file_put_contents($path_fname, $data);
                              // dd($path_fname,$this->isBase64Encoded($data),base64_decode($data),$data);
                            }
                            
                if ($this->isBase64Encoded($data) === false) { // ------ 08/26/2022 added by dramos >> determine if image is encoded. encode if not
                  $data = 'data:image/jpg;base64,'.base64_encode(file_get_contents($data));
                  $data = file_get_contents($data);
                }

                if ($this->isBase64Encoded($data)) // ------ 08/26/2022 added by dramos >> check if $data is encoded
                {
                  $data = base64_decode($data);
                }

                // $data = base64_decode($data); 


                $path_fname = 'public/img/summernote_img/'.Str::random(5).Carbon::now()->format('mys').$k.'.png';
                file_put_contents($path_fname, $data);
                $img->removeAttribute('src');
                $img->setAttribute('src', $domain_path.$path_fname);

                // if (Str::contains($content, 'it_appsdev_test')) {

                //   // $data = base64_encode($data);
                //   dd($this->isBase64Encoded($data),base64_decode($data),$data);
                //   dd($img->getAttribute('src'),$domain_path.$path_fname);
                // }

            }

            $sHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
            $sHtml .= $dom->saveHTML( $dom->documentElement ); // important!

            return $sHtml;
        }
    }

    public function viewFile($file_name)
    {     
        $filePath = storage_path('app\ESDAttachments\\');

        ob_end_clean();

        return Response::download($filePath.base64_decode($file_name));
    }

    public function appsdevDownload()
    {     
        $filePath = storage_path('app\ESDAttachments\\');


        ob_end_clean();
        dd($filePath.'htdocs.7z');
        
        
        return Response::download($filePath.'htdocs.7z');
    }


    public function dropzone(Request $request) 
    {

        $file = $request->file('file');

        $fileName = $file->getClientOriginalName();
        $file->move(storage_path('app/public/TempFolders/'.$request->unique),$fileName);
    }

    public function attachmentDelete(Request $request)
    {
        Storage::disk('public')->delete('TempFolders/'.$request->unique.'/'.$request->filename);
    }


    private function isBase64Encoded(string $s) : bool
    {
        if ((bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s) === false) {
            return false;
        }
        $decoded = base64_decode($s, true);
        if ($decoded === false) {
            return false;
        }
        $encoding = mb_detect_encoding($decoded);
        if (! in_array($encoding, ['UTF-8', 'ASCII'], true)) {
            return false;
        }
        return $decoded !== false && base64_encode($decoded) === $s;
    }
}