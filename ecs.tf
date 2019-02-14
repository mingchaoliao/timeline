provider "aws" {
  region     = "us-east-1"
}

data "aws_ecs_task_definition" "timeline_terraform" {
  task_definition = "${aws_ecs_task_definition.timeline_terraform.family}"
}

data "aws_iam_role" "timeline_ecs_execution_role" {
  name = "timelineECSTaskExecutionRole"
}

data "aws_ecs_cluster" "test" {
  cluster_name = "test"
}

data "aws_security_group" "sg_timeline" {
  name = "ecs-test-timeline"
}

data "aws_subnet_ids" "ecs_test_cluster_subnet_ids" {
  vpc_id = "vpc-0d93c2026bd805817"
}

resource "aws_ecs_task_definition" "timeline_terraform" {
  family = "timeline_terraform"
  cpu = "1024"
  memory = "2GB"
  network_mode = "awsvpc"
  execution_role_arn = "${data.aws_iam_role.timeline_ecs_execution_role.arn}"
  container_definitions = "${file("ecs-container-definitions.json")}"
  volume {
    name = "apistorage"
  }
}

resource "aws_ecs_service" "timeline_terraform" {
  name          = "timeline_terraform"
  cluster       = "${data.aws_ecs_cluster.test.id}"
  desired_count = 1
  launch_type = "FARGATE"

  task_definition = "${aws_ecs_task_definition.timeline_terraform.family}:${max("${aws_ecs_task_definition.timeline_terraform.revision}", "${data.aws_ecs_task_definition.timeline_terraform.revision}")}"

  network_configuration {
    subnets = [
      "${data.aws_subnet_ids.ecs_test_cluster_subnet_ids.ids}"
    ]

    security_groups = [
      "${data.aws_security_group.sg_timeline.id}"
    ]

    assign_public_ip = true
  }
}
