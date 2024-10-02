<form action="{{ route('update') }}"   method = "POST"enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="text" placeholder="name" name="name"><br>
    <input type="text" placeholder="email" name ="email"><br>
    <input type="text" placeholder="password" name="password"><br>
    <input type="text" placeholder="Confirm Password" name="password_confirmation"><br>
<input type="file" name="image" ><br>
<input type="submit">
</form>
