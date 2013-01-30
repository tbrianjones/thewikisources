# configure the host for the events index
es_host='http://ec2-54-234-115-6.compute-1.amazonaws.com:9200/'

# delete the nsns index
curl -XDELETE "${es_host}/events/?pretty=true"

# sleep to let elasticsearch delete complete
sleep=2
echo $'\n\n' "sleeping for $sleep seconds to let the index delete" $'\n\n'
sleep $sleep

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
			"year" : { "type" : "date", "format" : "year" },
			"context" : { "type" : "string", "boost" : 10.0 },
			"article" : {
				"properties" : {
					"id" : { "type" : "integer", "include_in_all" : "false" },
					"title" : { "type" : "string", "boost" : 5.0 },
					"brief" : { "type" : "string", "boost" : 2.0 }
				}
			}
		}
	}
}'