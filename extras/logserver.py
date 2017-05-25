#!flask/bin/python
from flask import abort, Flask, jsonify, request

app = Flask(__name__)

log = ".:: Global Log ::.\n"

#@app.route('/log/<string:task_id>', methods=['GET'])
#def get_task(task_id):
    # if len(task) == 0:
    #    abort(404)
    # If we want to keep global log:
    # global log
    # log = log + "\n" + task_id
#    print(task_id)
#    return task_id

@app.route('/message/', methods=['POST'])
def create_task():
    if not request.json or not 'message' in request.json:
        abort(400)
    print(request.json['message'])
    summary = {
        'message': request.json['message'],
    }
    return jsonify({'message': summary}), 201

if __name__ == '__main__':
    app.run(debug=True)

