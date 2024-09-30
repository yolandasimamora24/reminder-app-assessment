@section('content')
<div class="container-fluid animated fadeIn">

          	
          <div class="row">
	<div class="col-md-8 bold-labels">
		

		
		  <form method="post" action="https://portal.dev/admin/user/1">
		  <input type="hidden" name="_token" value="EXD3QGbKSmfYd1Ul7ZQNBGNjC3mZj44GLN6FkwiD">
		  <input type="hidden" name="_method" value="PUT">

		  			      
		      		      	<input type="hidden" name="_http_referrer" value="https://portal.dev/admin/user">


  <div class="card">
    <div class="card-body row">
      <div class="form-group col-md-4 required" element="div" bp-field-wrapper="true" bp-field-name="first_name" bp-field-type="text">
    <label>First Name</label>
    
                    <input type="text" name="first_name" value="Hazel" class="form-control">
            
    
    </div>    <div class="form-group col-md-4" element="div" bp-field-wrapper="true" bp-field-name="middle_name" bp-field-type="text">
    <label>Middle Name</label>
    
                    <input type="text" name="middle_name" value="" class="form-control">
            
    
    </div>    <div class="form-group col-md-4 required" element="div" bp-field-wrapper="true" bp-field-name="last_name" bp-field-type="text">
    <label>Last Name</label>
    
                    <input type="text" name="last_name" value="Alegbeleye" class="form-control">
            
    
    </div>    <div class="form-group col-md-6 required" element="div" bp-field-wrapper="true" bp-field-name="email" bp-field-type="email">
    <label>Email</label>
    
                    <input type="email" name="email" value="email@test.com" class="form-control">
            
    
    </div>    <div class="form-group col-md-6 required" element="div" bp-field-wrapper="true" bp-field-name="mobile_number" bp-field-type="text">
    <label>Phone</label>
    
                    <input type="text" name="mobile_number" value="+12345678" placeholder="+1 XXX XXX XXXX" class="form-control">
            
    
    </div>    <div class="form-group col-md-6 required" element="div" bp-field-wrapper="true" bp-field-name="dob" bp-field-type="date">
    <label>Date of Birth</label>
    
                    <input type="date" name="dob" value="2000-01-01" class="form-control">
            
    
    </div>    <div class="form-group col-md-6 required" element="div" bp-field-wrapper="true" bp-field-name="gender" bp-field-type="enum">
    <label>Gender</label>
        <select name="gender" class="form-control">

                    <option value="">-</option>
        
                                                <option value="Male (M)">Male (M)</option>
                                    <option value="Female (F)" selected="">Female (F)</option>
                                    <option value="Unspecified (U)">Unspecified (U)</option>
                                    <option value="Undisclosed (X)">Undisclosed (X)</option>
                                    <option value="Prefer Not to Answer">Prefer Not to Answer</option>
                                </select>

    
    </div>    <div class="form-group col-md-6 required" element="div" bp-field-wrapper="true" bp-field-name="race" bp-field-type="enum">
    <label>Race</label>
        <select name="race" class="form-control">

        
                                                <option value="American Indian and Alaskan Native">American Indian and Alaskan Native</option>
                                    <option value="Asian">Asian</option>
                                    <option value="Black or African American">Black or African American</option>
                                    <option value="Native Hawaiian and other Pacific Islander">Native Hawaiian and other Pacific Islander</option>
                                    <option value="Two or more races">Two or more races</option>
                                    <option value="White">White</option>
                                    <option value="Other" selected="">Other</option>
                                    <option value="Prefer not to answer">Prefer not to answer</option>
                                    <option value="Unknown">Unknown</option>
                                </select>

    
    </div>    <div class="form-group col-md-6 required" element="div" bp-field-wrapper="true" bp-field-name="ethnicity" bp-field-type="enum">
    <label>Ethnicity</label>
        <select name="ethnicity" class="form-control">

        
                                                <option value="Hispanic or Latino">Hispanic or Latino</option>
                                    <option value="Not Hispanic or Latino">Not Hispanic or Latino</option>
                                    <option value="Other" selected="">Other</option>
                                    <option value="Prefer not to answer">Prefer not to answer</option>
                                    <option value="Unknown">Unknown</option>
                                </select>

    
    </div>    <div class="form-group col-sm-12 required" element="div" bp-field-wrapper="true" bp-field-name="user_type" bp-field-type="enum">
    <label>User Type</label>
        <select name="user_type" class="form-control">

        
                                                <option value="admin" selected="">admin</option>
                                    <option value="patient">patient</option>
                                    <option value="provider">provider</option>
                                    <option value="dependent">dependent</option>
                                </select>

    
    </div>    <div class="hidden" element="div" bp-field-wrapper="true" bp-field-name="id" bp-field-type="hidden">
  <input type="hidden" name="id" value="1" class="form-control">
</div>
    </div>
  </div>





                            
            <div class="d-none" id="parentLoadedAssets">[]</div>
            <div id="saveActions" class="form-group">

        <input type="hidden" name="_save_action" value="save_and_back">
                    <div class="btn-group" role="group">
        
        <button type="submit" class="btn btn-success">
            <span class="la la-save" role="presentation" aria-hidden="true"></span> &nbsp;
            <span data-value="save_and_back">Save and back</span>
        </button>

        <div class="btn-group" role="group">
                            <button id="bpSaveButtonsGroup" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span><span class="sr-only">▼</span></button>
                <div class="dropdown-menu" aria-labelledby="bpSaveButtonsGroup">
                                    <button type="button" class="dropdown-item" data-value="save_and_edit">Save and edit this item</button>
                                    <button type="button" class="dropdown-item" data-value="save_and_new">Save and new item</button>
                                </div>
                    </div>

                    </div>
        
                    <a href="https://portal.dev/admin/user" class="btn btn-default"><span class="la la-ban"></span> &nbsp;Cancel</a>
        
            </div>

		  </form>
	</div>
</div>
          
          	
        </div>
@endsection