<!DOCTYPE html>
<html>
<head>
	<title></title>
    <meta name="_csrf" th:content="${_csrf.token}"/>
    <meta name="_csrf_parameter_name" th:content="${_csrf.parameterName}"/>
</head>
<body>
	<form action="{{route('checkingFileDocuments')}}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
		Upload File : 
		<input type="file" name="doc_file" id="doc_file">
		<button type="submit" name="submit">Submit</button>
	</form>
</body>
</html>