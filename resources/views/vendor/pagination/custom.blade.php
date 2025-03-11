@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Tombol Previous --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a>
                </li>
            @endif

            {{-- Tombol Halaman --}}
            @php
                $current = $paginator->currentPage();
                $last = $paginator->lastPage();

                // Tentukan rentang tombol yang akan ditampilkan
                if ($last <= 3) {
                    // Jika total halaman <= 3, tampilkan semua halaman
                    $start = 1;
                    $end = $last;
                } else {
                    // Jika total halaman > 3, tampilkan 3 tombol
                    if ($current == 1) {
                        // Jika di halaman 1, tampilkan 1, 2, 3
                        $start = 1;
                        $end = 3;
                    } elseif ($current == $last) {
                        // Jika di halaman terakhir, tampilkan $last-2, $last-1, $last
                        $start = $last - 2;
                        $end = $last;
                    } else {
                        // Jika di halaman tengah, tampilkan $current-1, $current, $current+1
                        $start = $current - 1;
                        $end = $current + 1;
                    }
                }
            @endphp

            {{-- Tampilkan tombol halaman --}}
            @for ($i = $start; $i <= $end; $i++)
                @if ($i == $current)
                    <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                    </li>
                @endif
            @endfor

            {{-- Tombol Next --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
