<template>
    <div>
        <!-- Not loading -->
        <div v-if="isLoading === false">
            <!-- There was an error -->
            <div v-if="isError != null">
                <div class="alert alert-dark mt-4" role="alert">
                    {{isError}}
                </div>
            </div>
            <!-- No errors -->
            <div v-else>
                <div class="card mt-4">
                    <!-- Header -->
                    <div class="card-header">
                        <h2 v-if="title != null">{{title}}</h2>
                        <p v-if="volumeId != null">ID: {{volumeId}}</p>
                    </div>
                    <!-- Body -->
                    <div class="card-body">
                        <!-- Buttons -->
                        <div class="text-right">
                            <!-- Add -->
                            <button v-if="bookId === 0" class="btn btn-primary" @click.prevent="storeBook()">
                                <i class="fas fa-plus"></i> Add to List
                            </button>  
                             <!-- Remove -->
                            <button v-else class="btn btn-danger" @click.prevent="removeBook(bookId)">
                                <i class="fas fa-trash"></i> Remove from List
                            </button>  
                        </div>
                        <!-- Author -->
                        <div v-if="author != null" class="mb-3">
                            Author: {{author}}
                        </div>
                        <!-- Image -->
                        <div v-if="image != null" class="mb-3">
                            <img :src="image" />
                        </div>
                        <!-- Description -->
                        <div v-if="description != null">
                            {{description}} 
                        </div>
                        <!-- No description -->
                        <div v-else>
                            There is no description for this book.
                        </div>
                    </div>
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
        props: ['id'],
        mounted() {
            this.getBook()
        },
        data() {
            return {
                volumeId: null,
                bookId: 0,
                title: null,
                author: null,
                description: null,
                image: null,
                isLoading: false,
                isError: null
            }
        },
        methods: {
            // Is book in user's reading list?
            bookStatus: function() {
                this.isLoading = true
                axios.get('/web-api/get/book-status/'+this.volumeId)
                .then((response) => {
                    this.bookId = response.data
                    this.isLoading = false
                });
            },
            // Get the book data from the Google API
            getBook: function() {
                this.isLoading = true
                axios.get('/api/get/book-from-google/'+this.id).then((response) => {
                    // Validate the data in the response
                    this.validateBook(response.data)
                    this.isLoading = false
                });                
            },
             // Validate the data in the API response
            validateBook: function(res) {
                // Set var to check the validity of the volume id in the response
                let isVolumeIdValid = false
                // If there is a response loop and set component vars
                if(res.items.length > 0) {
                    for(var i=0;i<res.items.length;i++) {
                        // If the volume id in props matches a volume id from Google API response
                        if(res.items[i].id === this.id) {
                            isVolumeIdValid = true
                            this.volumeId = res.items[i].id
                            this.title = res.items[i].volumeInfo.title
                            this.description = res.items[i].volumeInfo.description
                            // Is there an author?
                            if(res.items[i].volumeInfo.authors) {
                                this.author = res.items[i].volumeInfo.authors[0]
                            }
                            // Is there an image?
                            if(res.items[i].volumeInfo.imageLinks) {
                                this.image = res.items[i].volumeInfo.imageLinks.thumbnail
                            }                            
                        } 
                    }
                }
                // The volume id in props does not match any volume id in the Google API response. 
                if(isVolumeIdValid === false) {
                    this.isError = 'There was an issue with the data we received from Google. Please try another book.'
                } 
            },
            // Store the book to the user's reading list
            storeBook: function() {
                this.isLoading = true
                axios.post('/web-api/post/book', {
                    google_id: this.volumeId,
                    title: this.title,
                    author:this.author,
                    description: this.description,
                    image: this.image,
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
                    // Update the book status
                    this.bookStatus()
                    this.isLoading = false
                })
                
            },
            // Remove book from user's reading list
            removeBook: function(id) {
                axios.get('/web-api/destroy/book/'+id)
                .then((response) => {
                    // Notification
                    new Noty({
                      theme: 'relax',
                      type: 'success',
                      text: 'You have removed a book from your list!',
                      timeout: 6000,
                    }).show();
                    // Update the book status
                    this.bookStatus()
                })                
            },
        },
        watch: {
            // Watch for changes in the volume id
            volumeId: function() {
                if(this.volumeId != null) {
                    // Check the book status
                    this.bookStatus()
                }                
            }
        }
    }
</script>
