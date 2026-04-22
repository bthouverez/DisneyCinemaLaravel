<?php

namespace App\Livewire\Gallery;

use App\Models\Book;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;


class Index extends Component
{
    public bool $edit;

    public function mount()
    {
        $this->edit = request()->has('edit');
    }

    #[Computed]
    public function books()
    {
        return Book::all()->groupBy('status')->sortKeysDesc();
    }

    public function updateStatus(int $id, string $status)
    {
        Book::find($id)->update(['status' => $status]);
    }

    public function scrap()
    {
        $baseUrl = 'https://www.hachetteheroes.com/theme/25/disney-cinema?page=';
        $pages = env('NB_PAGES_TO_SCRAP'); // Number of pages to scrap
        for ($i = 1; $i <= $pages; $i++) {
            $url = $baseUrl . $i;
            $this->scrapePage($url);
        }

        return redirect('/')->with('success', 'Collection chargée');
    }

    public function delete(Book $book)
    {
        $book->delete();
    }

    public function scrapePage($url) {
        $browser = new HttpBrowser(HttpClient::create());
        $crawler = $browser->request("GET", $url);

        $crawler->filter('.container-product')->each(function ($node) {
            $title = $node->filter('.p-i-t')->text();
            $title = explode(' - ', $title)[0];
            $image = $node->filter('.product_picture')->attr('src');
            $link = $node->filter('.container-product a')->attr('href');
            $isbn = explode('/', $link)[3];
            if(!Book::where('isbn', $isbn)->exists()) {
                Book::create([
                    'title' => $title,
                    'image' => $image,
                    'link' => $link,
                    'isbn' => $isbn,
                    'status' => 'unpossessed'
                ]);
            }
        });

    }

    public function render()
    {
        return view('livewire.gallery.index');
    }
}
