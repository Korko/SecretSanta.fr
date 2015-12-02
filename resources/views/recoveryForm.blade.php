	<html>
		<body>
			<form action="#" method="post">
				{{ csrf_field() }}
				<label>
					Qui es-tu ?
					<select name="from" required>
						@foreach($users as $user => $numero)
							<option value="{{ $user }}">{{ $user }}</option>
						@endforeach
					</select>
				</label>
				<label>
					A qui dois-tu offrir ?
					<select name="to" required>
						<option>???</option>
						@foreach($users as $user => $numero)
							<option value="{{ $user }}">{{ $user }}</option>
						@endforeach
					</select>
				</label>
				<input type="submit" value="Valider">
			</form>
		</body>
	</html>
