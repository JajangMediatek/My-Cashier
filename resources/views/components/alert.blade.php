  @if ($errors->any())
    <div class="alert alert-{{ $type }}">
        @foreach ($errors->all() as $error)
        <small class="d-block">{{ $error }}</small>
        @endforeach
    </div>
  @endif