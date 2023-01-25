// delegate
import mx.utils.Delegate;
// ui components
import mx.controls.DataGrid
import mx.controls.Button
// file reference
import flash.net.FileReferenceList;
import flash.net.FileReference;

import flash.external.ExternalInterface;

import mx.controls.gridclasses.DataGridColumn;

import flash.events.Event;

class com.vixiom.utils.MultipleUpload
{
	
	private var fileRef:FileReferenceList;
	private var fileRefListener:Object;
	private var list:Array;
	
	private var files_dg:DataGrid;
	private var browse_btn:Button;
	private var upload_btn:Button;
	
	private var uploading_url:String;
	
	private var uploadingIntervalId:Number = 0;
	private var currentUploadingFile:Number = 0;
	
	private var uploaded_files:Number = 0;
		
	//////////////////////////////////////////////////////////////////////
	//
   	// Constructor (files_dg, browse_btn, upload_btn)
	//
	//////////////////////////////////////////////////////////////////////
	
	public function MultipleUpload(fdg:DataGrid, bb:Button, ub:Button) 
	{		
		// references for objects on the stage
		files_dg = fdg;
		browse_btn = bb;
		upload_btn = ub;
		
		// file list references & listener
		fileRef = new FileReferenceList();
		fileRefListener = new Object();
		fileRef.addListener(fileRefListener);
		
		// setup
		iniUI();
		inifileRefListener();								
	}
	
	//////////////////////////////////////////////////////////////////////
	//
   	// iniUI
	//
	//////////////////////////////////////////////////////////////////////
	
	private function iniUI()
	{
		// buttons
		browse_btn.onRelease = Delegate.create(this, this.browse);
		upload_btn.onRelease = Delegate.create(this, this.upload);

		// columns for dataGrid
		
		var _name_dgc:DataGridColumn = new DataGridColumn("name");
		_name_dgc.width = 100;
		_name_dgc.headerText = "Имя файла";
		
		var _size_dgc:DataGridColumn = new DataGridColumn("size");
		_size_dgc.width = 100;
		_size_dgc.headerText = "Размер";

		var _status_dgc:DataGridColumn = new DataGridColumn("status");
		_status_dgc.width = 100;
		_status_dgc.headerText = "Статус";
		
		files_dg.addColumn(_name_dgc);
		files_dg.addColumn(_size_dgc);
		files_dg.addColumn(_status_dgc);		
	}
	
	private function browse()
	{		
		var allTypes:Array	 = new Array();
				
		allTypes.push( getFileTypes() );
				
		fileRef.browse(allTypes);
	}
	
	private function upload()
	{
		getUploadingURL();
				
		file_uploading();
	}
	
	private function file_uploading()
	{		
		clearInterval( uploadingIntervalId );
		
		if( list.length > 0 )
		{		
			var file = list[ uploaded_files++ ];
		
			file.addListener(this);

			file.upload( uploading_url );
		}
	}
	
	//////////////////////////////////////////////////////////////////////
	//
   	// inifileRefListener
	//
	//////////////////////////////////////////////////////////////////////
	
	private function inifileRefListener()
	{
		fileRefListener.onSelect		= Delegate.create(this, this.onSelect);
		fileRefListener.onCancel		= Delegate.create(this, this.onCancel);
		fileRefListener.onOpen			= Delegate.create(this, this.onOpen);
		fileRefListener.onProgress		= Delegate.create(this, this.onProgress);
		fileRefListener.onComplete		= Delegate.create(this, this.onComplete);
		fileRefListener.onHTTPError		= Delegate.create(this, this.onHTTPError);
		fileRefListener.onIOError		= Delegate.create(this, this.onIOError);
		fileRefListener.onSecurityError	= Delegate.create(this, this.onSecurityError);
	}
	
	//////////////////////////////////////////////////////////////////////
	//
   	// onSelect
	//
	//////////////////////////////////////////////////////////////////////
	
	private function onSelect(fileRefList:FileReferenceList)
	{
		var max_file_size = getMaxFileSize();		
		
		// list of the file references
		list = fileRefList.fileList;
		
		// data provider list so we can customize things
		var list_dp = new Array();
		
		for(var i:Number = 0; i < list.length; i++) 
		{
			if( list[i].size > max_file_size )
			{
				list.splice( i, 1 );
			}
    	}		
		
		// loop over original list, convert bytes to kilobytes
		for(var i:Number = 0; i < list.length; i++) 
		{
			list_dp.push({name:list[i].name, size:Math.round(list[i].size / 1000) + " kb", status:"Готов к загрузке"});
    	}
		
		// display list of files in dataGrid
		files_dg.dataProvider = list_dp;
		files_dg.spaceColumnsEqually();
	}
	
	//////////////////////////////////////////////////////////////////////
	//
   	// onCancel
	//
	//////////////////////////////////////////////////////////////////////
	
	private function onCancel()
	{
		//
	}
	
	//////////////////////////////////////////////////////////////////////
	//
   	// onOpen
	//
	//////////////////////////////////////////////////////////////////////
	
	private function onOpen(file:FileReference)
	{
		//
	}
	
	//////////////////////////////////////////////////////////////////////
	//
   	// onProgress
	//
	//////////////////////////////////////////////////////////////////////
	
	private function onProgress(file:FileReference, bytesLoaded:Number, bytesTotal:Number)
	{		
		for(var i:Number = 0; i < list.length; i++) 
		{
			if (list[i].name == file.name) {
				var percentDone = Math.round((bytesLoaded / bytesTotal) * 100)
				files_dg.editField(i, "status", "Идет загрузка: " + ( percentDone - 1 ) + "%");
			}
    	}
	}
	
	//////////////////////////////////////////////////////////////////////
	//
   	// onComplete
	//
	//////////////////////////////////////////////////////////////////////
	
	private function onComplete(file:FileReference)
	{
		for(var i:Number = 0; i < list.length; i++) 
		{
			if (list[i].name == file.name) {
				files_dg.editField(i, "status", "Загружен");
				//all_files = all_files - 1;
			}
    	}
		
		if( uploaded_files == list.length )
		{			
			JSready();
		} else {
//			_global.setTimeout( file_uploading, 1000 );
			uploadingIntervalId = setInterval( this, "file_uploading", 1000 );			
		}
	}
	
	//////////////////////////////////////////////////////////////////////
	//
   	// onHTTPError
	//
	//////////////////////////////////////////////////////////////////////
	
	private function onHTTPError(file:FileReference, httpError:Number)
	{
		//
	}
	
	//////////////////////////////////////////////////////////////////////
	//
   	// onIOError
	//
	//////////////////////////////////////////////////////////////////////
	
	private function onIOError(file:FileReference)
	{
		//
	}
	
	//////////////////////////////////////////////////////////////////////
	//
   	// onSecurityError
	//
	//////////////////////////////////////////////////////////////////////
	
	private function onSecurityError(file:FileReference, errorString:String)
	{
		//
	}

	//////////////////////////////////////////////////////////////////////
	//
   	// Calling JavaScript function when all is completed
	//
	//////////////////////////////////////////////////////////////////////
	
	private function JSready()
	{			
		if( ExternalInterface.available )
		{
			ExternalInterface.call("flash_multiupload_complete", '' );				
		}
	}	
	
	//////////////////////////////////////////////////////////////////////
	//
   	// Getting the URL to uploading
	//
	//////////////////////////////////////////////////////////////////////
	
	private function getUploadingURL()
	{	
		if( ExternalInterface.available )
		{
			var sAddressVar:Object = ExternalInterface.call( "flash_get_uploading_url", '' );
			uploading_url = String( sAddressVar );				
		}
	}	
	
	//////////////////////////////////////////////////////////////////////
	//
   	// Getting allowed file types
	//
	//////////////////////////////////////////////////////////////////////
	
	private function getFileTypes()
	{	
		var fileTypes:Object = new Object();
		
		if( ExternalInterface.available )
		{
			var types_name:Object = ExternalInterface.call( "get_file_types", 'name' );			
			var types_list:Object = ExternalInterface.call( "get_file_types", 'list' );						
			
			if( types_list != null )
			{			
				fileTypes.description 	= String( types_name );
				fileTypes.extension 	= String( types_list );
			}
			else
			{
				fileTypes.description 	= String( 'Неудается получить список файлов.' );
				fileTypes.extension 	= String( '3fs474t.df453grfd' );													
			}						
		}
		else
		{
			fileTypes.description 	= String( 'Невозможно закачивать файлы.' );
			fileTypes.extension 	= String( '3fs474t.df453grfd' );													
		}

		return fileTypes;
	}		
	
	//////////////////////////////////////////////////////////////////////
	//
   	// Get max allowed filesize ( in bytes )
	//
	//////////////////////////////////////////////////////////////////////
	
	private function getMaxFileSize()
	{	
		var max_file_size:Number = 0;
	
		if( ExternalInterface.available )
		{
			var filesize:Object = ExternalInterface.call( "get_max_file_size", '' );
			
			if( filesize != null )
			{
				max_file_size = Number( filesize );
			}			
		}
		
		return max_file_size;
	}
		
}