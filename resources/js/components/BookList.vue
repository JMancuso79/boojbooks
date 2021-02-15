<template>
    <div>
        <!-- Not loading -->
        <div v-if="isLoading === false">
            <!-- Filter Results -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row mb-3">
                        <!-- Clear form -->
                        <div class="col-12 text-right">
                            <button class="btn btn-secondary" @click.prevent="resetBooks()">
                                Clear
                            </button>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <!-- Author search -->
                        <div class="col-12">
                            <label for="author-search" class="form-label">Search by author</label>
                            <input type="text" class="form-control" id="author-search" v-model="keyword" placeholder="Enter author name">
                        </div>
                        <!-- Authors that meatch search query -->
                        <div class="col-12 pt-3">
                            <div v-for="matchedAuthor in matchedAuthors" class="pt-3 pl-3 pb-3 mb-1 bg-light pointer text-capitalize" @click.prevent="author = matchedAuthor">
                                {{matchedAuthor}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Filter by category -->
                        <div class="col-12 col-md-6">
                            <div>
                                Filter by category
                            </div>
                            <!-- Category results -->
                            <div v-for="category in categories">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" :value="category" v-model="checkedCategories" id="category-checkbox">
                                    <label class="form-check-label" for="category-checkbox">
                                        {{category}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- Change the order of the results -->
                        <div class="col-12 col-md-6 text-right">
                            <div>
                                Change list order
                            </div>
                            <!-- Ordering Options -->
                            <select class="form-select" v-model="order">
                                <option selected>Select one</option>
                                <option value="asc">Alphabetical (A-Z)</option>
                                <option value="desc">Alphabetical (Z-A)</option>
                                <option value="newest">Publish Date (Newest)</option>
                                <option value="oldest">Publish Date (Oldest)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <!-- If an author has been selected -->
            <div v-if="author != null && author != ''" class="pb-3">
                <button class="btn btn-secondary chip text-capitalize" @click.prevent="author = null">
                    {{author}} <i class="fas fa-times-circle"></i>
                </button>
            </div>
            <!-- Books from Google Books API -->
            <div v-if="sortedBooks.length > 0" class="row">
                <div v-for="book in sortedBooks" class="col-6 col-md-4 col-lg-2 mb-3">
                    <!-- Book Image -->
                    <p v-if="book.image != null" class="text-center">
                        <a :href="'/book-details/'+book.volumeId">
                            <img :src="book.image" height="180px" width="100%" />
                        </a>
                    </p>
                    <!-- Book Title & Date -->
                    <div v-if="book.title != null" class="book-title-small">
                         <a :href="'/book-details/'+book.volumeId" style="text-decoration:none;">
                            {{book.title.substring(0,16)}}<span v-if="book.title.length > 16">...</span>
                        </a>
                        <!-- Published Date -->
                        <br>{{book.publishedDate}}
                        <br><span style="font-size:10px;">{{book.category}}</span>
                    </div>
                    <!-- Author -->
                    <div v-if="book.author != null">
                        {{book.author.substring(0,16)}}<span v-if="book.author.length > 16">...</span>
                    </div>
                    <!-- Add to user's reading list -->
                    <p v-if="book.inUserBookList === false">
                        <button class="btn btn-primary btn-block" @click.prevent="storeBook(book)">
                            Add to list
                        </button>                   
                    </p>
                    <!-- Remove from user's reading list -->
                    <p v-if="book.inUserBookList === true" @click.prevent="removeBook(book.volumeId)">
                        <button class="btn btn-danger btn-block">
                            Remove
                        </button>                   
                    </p>
                </div>
            </div>
            <!-- No query results -->
            <div v-else>
                <div class="alert alert-dark" role="alert">
                    There are no books that match your query.
                </div>                
            </div>
        </div>
        <!-- Data is loading -->
        <div v-else class="text-center p-3">
            Loading...
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.getUserBookList()
            this.getBooks()
        },
        data() {
            return {
                books: [],
                categories: [],
                checkedCategories: [],
                authors: [],
                author: null,
                matchedAuthors: [],
                keyword: null,
                userBookList: [],
                order: 'asc',
                isLoading: false
            }
        },
        methods: {
            // Get books from Google API
            getBooks: function() {
                this.isLoading = true
                axios.get('/api/get/books-from-google')
                .then((response) => {
                    if(response.data) {
                        this.doBookList(response.data.items)
                    }
                    this.isLoading = false
                });
            },
            // Create the books object from Google results
            doBookList: function(items) {
                if(items.length > 0) {
                    for(var i=0;i<items.length;i++) {
                        // Set Image
                        let image = '/images/book-default.png'
                        if(items[i].volumeInfo.imageLinks) {
                            image = items[i].volumeInfo.imageLinks.thumbnail
                        } 
                        // Set Author
                        let author = 'Author Unknown'
                        if(items[i].volumeInfo.authors) {
                            if(items[i].volumeInfo.authors.length > 0) {
                                author = items[i].volumeInfo.authors[0]
                            }
                        }
                        // If this author isn't in authors object 
                        if(!this.authors.includes(author)) {
                            // Add to authors
                            this.authors.push(author)
                        }
                        // Set Category
                        let category = 'Uncategorized'
                        if(items[i].volumeInfo.categories) {
                            if(items[i].volumeInfo.categories.length > 0) {
                                category = items[i].volumeInfo.categories[0]
                            }
                        }
                        // If this category isn't in categories object 
                        if(!this.categories.includes(category)) {
                            // Add to categories
                            this.categories.push(category)
                        }
                        // Add this item to books 
                        this.books.push({
                            volumeId: items[i].id,
                            title: items[i].volumeInfo.title,
                            author: author,
                            publishedDate: items[i].volumeInfo.publishedDate,
                            description: items[i].volumeInfo.description,
                            image: image,
                            category: category,
                            inUserBookList: this.isBookInUserList(items[i].id)
                        })
                    }                    
                }
            },
            // Get volume ids from user's book list
            getUserBookList: function() {
                axios.get('/web-api/get/user-book-ids')
                .then((response) => {
                    this.userBookList = response.data
                })                
            },
            // Store this book in user's list
            storeBook: function(book) {
                axios.post('/web-api/post/book', {
                    google_id: book.volumeId,
                    title: book.title,
                    author: book.author,
                    description: book.description,
                    image: book.image,
                    order: 0
                }).then((response) => {
                    // Response message
                    let msg = null
                    if(response.data.message == 'success') {
                        msg = 'You have added a book to your list!'
                    }
                    if(response.data.message == 'fail-validation') {
                        msg = 'Something went wrong'
                    }
                    // Notifications
                    new Noty({
                      theme: 'relax',
                      type: response.data.message,
                      text: msg,
                      timeout: 6000,
                    }).show();
                    // Get user's updated book list
                    this.getUserBookList()
                })
            },
            // Remove book from user's list
            removeBook: function(id) {
                axios.get('/web-api/destroy/book-by-volume-id/'+id)
                .then((response) => {
                    // Notification
                    new Noty({
                      theme: 'relax',
                      type: 'success',
                      text: 'You have removed a book from your list!',
                      timeout: 6000,
                    }).show();
                    // Get user's updated book list
                    this.getUserBookList()
                })                
            },
            // Check if book is in user's list
            isBookInUserList: function(volumeId) {
                let inUserBookList = false
                if(this.userBookList.length > 0) {
                    for(var x=0;x<this.userBookList.length;x++) {
                        // If this book id is in the user's reading list
                        if(volumeId === this.userBookList[x]) {
                            inUserBookList = true
                        }
                    }
                } 
                return inUserBookList           
            },
            resetBooks: function() {
                this.checkedCategories = []
                this.keyword = null
                this.matchedAuthors = []
                this.author = null
            },
            // Change the order of the book list / This should be cleaned and less redundant
            orderBy: function(booksToReturn) {                
                // Order books alphabetically or by published date
                booksToReturn.sort((a, b) => {
                    // Order alphabetical a - z
                    if (this.order == 'asc') {
                        let fa = a.title.toLowerCase(), fb = b.title.toLowerCase()

                        if (fa < fb) {
                            return -1
                        }
                        if (fa > fb) {
                            return 1 
                        }
                        return 0
                    } 
                    // Order alphabetical z - a
                    if (this.order == 'desc') {
                        let fa = a.title.toLowerCase(), fb = b.title.toLowerCase()

                        if (fa > fb) {
                            return -1
                        }
                        if (fa < fb) {
                            return 1 
                        }
                        return 0
                    }  
                    // Order by published date oldest first
                    if (this.order == 'oldest') {
                        let fa = a.publishedDate, fb = b.publishedDate

                        if (fa < fb) {
                            return -1
                        }
                        if (fa > fb) {
                            return 1 
                        }
                        return 0
                    } 
                    // Order by published date newest first
                    if (this.order == 'newest') {
                        let fa = a.publishedDate, fb = b.publishedDate

                        if (fa > fb) {
                            return -1
                        }
                        if (fa < fb) {
                            return 1 
                        }
                        return 0
                    }                         
                })
                return booksToReturn
            }
        },
        computed: {
            // Sort and order book list / This should be broken down into several methods and cleaned up / no time to do that
            sortedBooks: function() {
                let booksToReturn = []
                let isAuthor = false
                let isCategory = false
                // If an author has been selected
                if(this.author != null && this.author != '') {
                    isAuthor = true
                }
                // If a category has been checked
                if(this.checkedCategories.length > 0) {
                    isCategory = true
                }
                // If there's an author but no category
                if(isAuthor === true && isCategory === false) {
                    for(var i=0;i<this.books.length;i++) {
                        var a = this.author.toLowerCase();
                        var b = this.books[i].author.toLowerCase();                        
                        if(a == b) {
                            booksToReturn.push(this.books[i])
                        }
                    }                    
                }
                // If there's an author and a category
                if(isAuthor === true && isCategory === true) {
                    let booksToSort = []
                    for(var i=0;i<this.books.length;i++) {
                        var a = this.author.toLowerCase();
                        var b = this.books[i].author.toLowerCase();                        
                        if(a == b) {
                            booksToSort.push(this.books[i])
                        }                       
                    }
                    if(booksToSort.length > 0) {
                        for(var x=0;x<booksToSort.length;x++) {
                            if(this.checkedCategories.includes(booksToSort[x].category)) {
                                booksToReturn.push(booksToSort[x])
                            }                                
                        }
                    } 
                }
                // If there's no author and there is a category
                if(isAuthor === false && isCategory === true) {
                    for(var i=0;i<this.books.length;i++) {
                        if(this.checkedCategories.includes(this.books[i].category)) {
                            booksToReturn.push(this.books[i])
                        }
                    }                    
                }
                // No author and not category
                if(isAuthor === false && isCategory === false) {
                    booksToReturn = this.books
                }

                booksToReturn = this.orderBy(booksToReturn)

                return booksToReturn
            }
        },
        watch: {
            // Watch for changes in user's book list 
            userBookList: {
                handler() {
                    if(this.books.length > 0) {
                        for(var i=0;i<this.books.length;i++) {
                            // Check if book is in user's list
                            this.books[i].inUserBookList = this.isBookInUserList(this.books[i].volumeId)
                        }
                    }
                },
                deep: true
            },
            keyword: function() {
                this.matchedAuthors = []
                if(this.keyword != null && this.keyword != '') {
                    for(var i=0;i<this.authors.length;i++) {
                        var a = this.authors[i].toLowerCase();
                        var k = this.keyword.toLowerCase();
                        if(a.includes(k)) {
                            this.matchedAuthors.push(a)
                        }
                    }                    
                }
            },
            author: function() {
                if(this.author != null && this.author != '') {
                    this.keyword = null
                    this.matchedAuthors = []
                }
            }
        }

    }
</script>