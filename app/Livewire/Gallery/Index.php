<?php

namespace App\Livewire\Gallery;

use App\Models\Book;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;


class Index extends Component
{
    public $edit;
    public array $status;

    public function mount()
    {
        $this->edit = request()->has('edit');
        foreach($this->books->keys() as $status ) {
            foreach($this->books->get($status) as $book)
                $this->status[$book->id] = $book->status;
        }
    }

    #[Computed]
    public function books()
    {
        return Book::all()->groupBy('status')->sortKeysDesc();
    }

    public function updatedStatus($status, $id)
    {
        $book = Book::find($id);
        $book->update(['status' => $status]);
    }

    public function scrap()
    {
        $baseUrl = 'https://www.hachetteheroes.com/theme/25/disney-cinema?page=';
        $pages = 5; // Number of pages to scrape
        for ($i = 1; $i <= $pages; $i++) {
            $url = $baseUrl . $i;
            $this->scrapePage($url);
        }
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
            if(Book::where('link', $link)->exists()) {
                $book = Book::where('title', $title)->first();
                $book->update([
                    'image' => $image,
                    'link' => $link,
                ]);
            } else {
                Book::create([
                    'title' => $title,
                    'image' => $image,
                    'link' => $link,
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
