<template>
    <div class="pt-4">
        <div class="card">
            <div class="card-header">
                <h2>My Book List</h2>
                <p>A list of books I'm interested in reading.</p>
            </div>

            <div class="card-body">
                <!-- Not Loading -->
                <div v-if="isLoading === false">
                    <!-- Reading list -->
                    <div v-if="userBooks.length > 0">
                        <!-- Info -->
                        <div class="alert alert-dark mb-3" role="alert">
                            Drag the book up or down to priortize your reading list!
                        </div>
                        <!-- The list -->
                        <draggable v-model="userBooks" @change="updateBookOrder()">
                            <transition-group>
                                <div v-for="element in userBooks" :key="element.id" class="row border-bottom pt-3 pb-3 mb-3">
                                    <!-- Image -->
                                    <div class="col-2 col-md-2 col-lg-1">
                                        <div v-if="element.image != null">
                                            <a :href="'/book-details/'+element.google_id">
                                                <img :src="element.image" width="100%" style="max-width: 60px;" />
                                            </a>
                                        </div>
                                        <div v-else class="text-center">
                                            No Image
                                        </div>
                                    </div>
                                    <!-- Title -->
                                    <div v-if="element.title != null" class="col-7  col-md-8 col-lg-9 pointer">
                                        <a :href="'/book-details/'+element.google_id">
                                            {{element.title}}
                                        </a>
                                    </div>
                                    <!-- Remove from reading list -->
                                    <div class="col-3 col-md-2 col-lg-2 text-right">
                                        <button class="btn btn-light" @click.prevent="removeBook(element.id)">
                                            <i class="fas fa-trash text-danger"></i>
                                        </button>
                                    </div>
                                </div>
                            </transition-group>
                        </draggable>
                    </div>
                    <!-- User has no books -->
                    <div v-if="userBooks.length < 1">
                        <div class="alert alert-dark" role="alert">
                          Your book list is empty! <a href="/">Click here to browse books.</a>
                        </div>
                    </div>
                </div>
                <!-- Loading -->
                <div v-else class="text-center p-3">
                    Loading...
                </div>
            </div>
        </div> 
    </div>
</template>

<script>
    import draggable from 'vuedraggable'
    export default {
        components: {
            draggable,
        },
        mounted() {
            this.getUserBooks()
        },
        data() {
            return {
                userBooks: [],
                isLoading: false
            }
        },
        methods: {
            // Get user's readling list in priority order
            getUserBooks: function() {
                this.isLoading = true
                axios.get('/web-api/get/user-books')
                .then((response) => {
                    this.userBooks = response.data
                    this.isLoading = false
                })
            },
            // Remove book from reading list
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
                    // Update the user book list
                    this.getUserBooks()
                })                
            },
            // Update the book order after item has been dragged
            updateBookOrder: function() {
                for (var i=0; i<this.userBooks.length;i++) {
                    axios.post('/web-api/update/book/' + this.userBooks[i].id, {
                        order: i
                    }).then((response) => {
                        //console.log(response.data)
                    })
                }
            },
            // Go to details page
            viewDetails: function(id) {
                window.location = '/book-details/' + id
            }
        },
        watch: {
            // Get user's reading list after order has been changed
            order: function() {
                this.getUserBooks()
            }
        }
    }
</script>

