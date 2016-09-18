@if($paginator->getLastPage() > 1)
    <div class="pagination">
        {{ with(new Acme\Pagination\Presenters\AcmePresenter($paginator))->render() }}
    </div>
@endif