import Vue from 'vue'
import JsonCSV from 'vue-json-csv'

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');



Vue.component('downloadCsv', JsonCSV)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));

const app = new Vue({
    el: '#app',
	data: {
		// для понимания того, какое row редактировать
		currentFieldId: 0,
		editCurrentField: false,
		// для Post запросов
		request: {
			fieldId: 0,			
			firstName: '',
			lastName: '',
			phone: '',
			observations: '',
		},	
		// для поиска на странице
		is_searching: false,
		searching: {
			searchQuery: '',
			resources: [],
			searchedNotes: []
		},
		is_ready_to_export_csv: false,
		json_data: []
	},
	methods: {
		// клик по кнопке Изменить
		clickEditButton: function(fieldId) {
			this.currentFieldId = fieldId;
			this.editCurrentField = true;	
		},
		// клик по кнопке Сохранить
		clickSaveButton: function(fieldId) {
			this.currentFieldId = fieldId;
			this.editCurrentField = false;

			// закидываем значеня со страницы в объект для post запросов
			this.request.fieldId = fieldId;
			this.request.firstName = this.$refs['refFirstName_'+fieldId+''].value;
			this.request.lastName = this.$refs['refLastName_'+fieldId+''].value;
			this.request.phone = this.$refs['refPhone_'+fieldId+''].value;
			this.request.observations = this.$refs['refObservations_'+fieldId+''].value;
			
			// посылаем пост запрос
			axios.post('/edit-note', {
			    data: this.request,
			    // выводим на странице сообщение с ответа
			}).then(response => (this.$refs.refMessageFromRequest.innerText = response.data[0].message));			
		},
		// клик по кнопке Создать
		clickAddButton: function() {

			// закидываем значеня со страницы в объект для post запросов
			this.request.firstName = this.$refs.refModalFirstName.value;
			this.request.lastName = this.$refs.refModalLastName.value;
			this.request.phone = this.$refs.refModalPhone.value;
			this.request.observations = this.$refs.refModalObservations.value;

			// посылаем пост запрос
			axios.post('/create-note', {
			    data: this.request,
			    // выводим на странице сообщение с ответа
			}).then(response => (this.$refs.refModalMessageFromRequest.innerText = response.data[0].message));			
		},				
		// клик по кнопке Удалить
		clickDeleteButton: function(fieldId) {
			this.currentFieldId = fieldId;
			this.editCurrentField = false;	

			// put values from inputs in request data
			this.request.fieldId = fieldId;
			
			// пост запрос на удаление
			axios
				.post('/delete-note', {data: this.request})
				// выводим сообщение на стр
				.then(response => (this.$refs.refMessageFromRequest.innerText = response.data[0].message))
				// перезагружаем стр
				.then(function(){
					setInterval(function() { 
						location.reload();
				 	}, 2000);
				});

		},
		keyupFiredOnChangeInputValue: function(e) {

		},
		// здесь логика работы Поиска на странице
		// Изначально у нас есть два массива в data
		// resources: [] - массив с notes со страницы (получаем через DOM)
		// searchedNotes: [] - массив с notes, которые отобраны по поисковому запросу
		keyupFiredOnSeachingInput: function(e) {

			this.searching.searchQuery = e.target.value;

			// работа с DOM содержимым для получения объекта с данными для поиска

			// получаем всю таблицу с ref из blade
			let notes_table = this.$refs.refNotesTbody.children;

			// приводим к массиву
		   	let notesObj = Object.keys(notes_table).map((key) => {
		    	return notes_table[key]
		   	})		

			// вытаскиваем HTMLCollection
		   	let tdObj = [];	
		   	notesObj.forEach(function(tr){
		   		tdObj.push([[].slice.call(tr.children)])
		   	});

			// теперь у нас есть tdObj массив с каждым tr, необходимо еще раз пройтись, чтобы вытащить каждый td
		   	let allNotes = [];
		   	tdObj.forEach(function(i){
		   		i.forEach(function(k){
		   			allNotes.push([[].slice.call(k)])	
		   		});
		   	});

		   	// финальное дейсвтие - с каждого td в каждом td вытягиваем соответ. значения
		   	let resources = [];
		   	allNotes.forEach(function(i){
		   		let row = ({
		   			noteId : i[0][0].innerText,
		   			firstName : i[0][1].lastChild.defaultValue,
		   			lastName : i[0][2].lastChild.defaultValue,
		   			phone : i[0][3].lastChild.defaultValue,
		   			observations : i[0][4].lastChild.defaultValue,
		   		});
		   		resources.push(row);
		   	});

		   	this.searching.resources = resources;

		   	// возврат значений

		   	// если введенных символов для поиска больше 1

	      	if (this.searching.searchQuery.length > 1) {

	      		this.is_searching = true;

	      		// присваиваем к переменной поисковый запрос
	      		let query_search = this.searching.searchQuery;

	      		// формируем общий массив найденных notes которые нужно будет вывести
	      		let searchedNotes = [];

	      		// пробегаемся по каждой существующей записи на странице
	      		this.searching.resources.forEach(function(k){

	      			// сравниваем First name
	      			if (
	      					k.noteId.startsWith(query_search) ||
	      					k.firstName.startsWith(query_search) ||
	      					k.lastName.startsWith(query_search) ||
	      					k.phone.startsWith(query_search) ||
	      					k.observations.startsWith(query_search) 
	      				) {
	      				

	      				// формируем строку
				   		let searched_row = ({
				   			noteId : k.noteId,
				   			firstName : k.firstName,
				   			lastName : k.lastName,
				   			phone : k.phone,
				   			observations : k.observations,
				   		});
				   		searchedNotes.push(searched_row);
	      			}
	      		});

	      		// присваиваем сформированный массив к data
	      		this.searching.searchedNotes = searchedNotes;

	   			} else if ( this.searching.searchQuery.length == 0) {
	        		this.is_searching = false;
	      		}
		},
		getLogsForExportCsv: function(){

				// работа с DOM содержимым для получения объекта с данными логов

				// получаем всю таблицу с ref из blade
				let notes_table = this.$refs.refNotesTbody.children;

				// приводим к массиву
			   	let notesObj = Object.keys(notes_table).map((key) => {
			    	return notes_table[key]
			   	})		

				// вытаскиваем HTMLCollection
			   	let tdObj = [];	
			   	notesObj.forEach(function(tr){
			   		tdObj.push([[].slice.call(tr.children)])
			   	});

				// теперь у нас есть tdObj массив с каждым tr, необходимо еще раз пройтись, чтобы вытащить каждый td
			   	let allNotes = [];
			   	tdObj.forEach(function(i){
			   		i.forEach(function(k){
			   			allNotes.push([[].slice.call(k)])	
			   		});
			   	});

			   	console.log(allNotes);

			   	// финальное дейсвтие - с каждого td в каждом td вытягиваем соответ. значения
			   	let resources = [];
			   	allNotes.forEach(function(i){
			   		let row = ({
			   			noteId : i[0][0].innerText,
			   			description : i[0][1].innerText,
			   			time : i[0][2].innerText,
			   		});
			   		resources.push(row);
			   	});		

		   	// для выгрузки CSV файла на странице /logs
		   	this.json_data = resources;	
		   	this.is_ready_to_export_csv = true;	
   		
		}		
	}
});
