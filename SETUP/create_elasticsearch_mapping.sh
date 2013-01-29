# configure the host for the events index
es_host='http://ec2-54-234-115-6.compute-1.amazonaws.com:9200/'

# create the events index and apply settings
curl -XPUT "${es_host}/events/?pretty=true" -d '{
	"index" : {
		"number_of_shards" : 5,
		"number_of_replicas" : 1
	}
}'

# create event mapping
curl -XPOST "${es_host}/events/event/_mapping?pretty=true" -d '{
	"event" : {
		"type" : "object",
		"include_in_all" : "true",
		"index" : "analyzed",
		"path" : "full",
		"properties" : {
			"id" : { "type" : "integer", "include_in_all" : "false" },
			"year" : { "type" : "integer", "boost" : 10 },
			"context" : { "type" : "string", "boost" : 10 },
			"article" : {
				"properties" : {
					"id" : { "type" : "integer", "include_in_all" : "false" },
					"title" : { "type" : "string", "boost" : 5 },
					"brief" : { "type" : "string", "boost" : 2 }
				}
			}
		}
	}
}'