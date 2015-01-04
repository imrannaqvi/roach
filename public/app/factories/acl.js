'use strict';

app.factory('ACL', function() {
	var AclData = {};
	return {
		isAllowed: function(resource) {
			if(typeof(AclData.resources[resource]) !== 'undefined') {
				return AclData.resources[resource];
			} else {
				throw('resource not defined.');
			}
		},
		set: function(data) {
			AclData = data;
		},
		get: function() {
			return AclData;
		}
	};
});