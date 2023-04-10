<form method="post" action="/upload" enctype="multipart/form-data">
    @csrf
    <label for="PDF">PDF Invoice
    </label>
    <input type="file" name="PDF" placeholder="PDF address" />
    <button type="submit">Add</button>
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
    @if (session()->has('success'))
        <div>
            {{ session('success') }}
        </div>
    @endif
</form>

<table>
    <tbody>
        <tr>
            <td>Arveid kokku:</td>
            <td>{{ $invoiceCount }} tk</td>
        </tr>
        <tr>
            <td>Keskmiselt tooteid arvel:</td>
            <td>{{ $averageAmountPerInvoice }} tk</td>
        </tr>
        <tr>
            <td>Keskmine toote hind:</td>
            <td>{{ $averageProductPrice }} €</td>
        </tr>
        <tr>
            <td>Keskmine toodete summa:</td>
            <td>{{ $averagePrice }} €</td>
        </tr>
    </tbody>
</table>
